<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Міграція даних зі старої бази в нову архітектуру.
 *
 * Запуск: php artisan db:seed --class=MigrateOldDataSeeder
 *
 * ВАЖЛИВО: запускати тільки один раз на порожній новій БД.
 * Перед запуском переконайтесь що php artisan migrate вже виконано.
 */
class MigrateOldDataSeeder extends Seeder
{
    // Маппінг старих статусів (code) → нові enum значення
    private array $statusMap = [
        'accepted'       => 'new',
        'in_work'        => 'in_progress',
        'diagnostic'     => 'diagnosed',
        'approval'       => 'approved',
        'waiting_parts'  => 'waiting_parts',
        'repair'         => 'in_progress',
        'ready'          => 'ready',
        'waiting_client' => 'ready',
        'finished'       => 'issued',
        'canceled'       => 'cancelled',
        'archived'       => 'issued', // архівне → вважаємо виданим
    ];

    public function run(): void
    {
        $this->command->info('');
        $this->command->info('=== Міграція даних зі старої бази ===');
        $this->command->info('');

        // Підключення до старої БД (налаштуй в config/database.php як "old")
        // Або читаємо напряму з SQL файлу через тимчасові таблиці
        $old = DB::connection('old');

        DB::transaction(function () use ($old) {

            // ──────────────────────────────────────────────
            // 1. ТИПИ ПРИСТРОЇВ (devices → device_types)
            // ──────────────────────────────────────────────
            $this->command->info('1/7 Міграція типів пристроїв...');

            $oldDevices = $old->table('devices')->get();
            $deviceTypeMap = []; // old_id → new_id

            foreach ($oldDevices as $d) {
                $newId = DB::table('device_types')->insertGetId([
                    'name'       => $d->name,
                    'icon'       => null,
                    'is_active'  => true,
                    'created_at' => $d->created_at,
                    'updated_at' => $d->updated_at,
                ]);
                $deviceTypeMap[$d->id] = $newId;
            }

            $this->command->info("   ✅ Перенесено типів пристроїв: " . count($deviceTypeMap));

            // ──────────────────────────────────────────────
            // 2. БРЕНДИ (brands → brands)
            // ──────────────────────────────────────────────
            $this->command->info('2/7 Міграція брендів...');

            $oldBrands = $old->table('brands')->get();
            $brandMap = []; // old_id → new_id

            foreach ($oldBrands as $b) {
                $newId = DB::table('brands')->insertGetId([
                    'name'           => $b->name,
                    'device_type_id' => null, // у старій БД не було прив'язки до типу
                    'logo'           => null,
                    'is_active'      => true,
                    'created_at'     => $b->created_at,
                    'updated_at'     => $b->updated_at,
                ]);
                $brandMap[$b->id] = $newId;
            }

            $this->command->info("   ✅ Перенесено брендів: " . count($brandMap));

            // ──────────────────────────────────────────────
            // 3. КЛІЄНТИ (contacts + counterparties → clients)
            // ──────────────────────────────────────────────
            $this->command->info('3/7 Міграція клієнтів...');

            $oldCounterparties = $old->table('counterparties')
                ->where('group_id', 1) // тільки клієнти (group 1 = Клієнти)
                ->get();

            $clientMap = []; // old counterparty_id → new client_id

            foreach ($oldCounterparties as $cp) {
                $contact = $old->table('contacts')->find($cp->contact_id);
                if (!$contact) continue;

                $newId = DB::table('clients')->insertGetId([
                    'user_id'         => null,
                    'type'            => $cp->type === 'company' ? 'legal' : 'individual',
                    'name'            => $contact->name,
                    'phone'           => $this->normalizePhone($contact->phone),
                    'email'           => $contact->email,
                    'tax_code'        => null,
                    'contact_person'  => null,
                    'contact_phone'   => null,
                    'city'            => null,
                    'address'         => $contact->address,
                    'notes'           => $contact->notes,
                    'is_vip'          => false,
                    'is_blacklisted'  => false,
                    'blacklist_reason'=> null,
                    'created_at'      => $cp->created_at,
                    'updated_at'      => $cp->updated_at,
                ]);

                $clientMap[$cp->id] = $newId;
            }

            $this->command->info("   ✅ Перенесено клієнтів: " . count($clientMap));

            // ──────────────────────────────────────────────
            // 4. ЗАМОВЛЕННЯ (orders → devices + orders + estimates)
            // ──────────────────────────────────────────────
            $this->command->info('4/7 Міграція замовлень...');

            $oldOrders = $old->table('orders')->get();
            $orderMap  = []; // old_id → new_id
            $skipped   = 0;

            // Отримуємо статуси для маппінгу
            $oldStatuses = $old->table('order_statuses')
                ->get()
                ->keyBy('id');

            // Отримуємо адміна (перший user) щоб призначити manager_id
            $adminId = DB::table('users')->first()?->id;

            foreach ($oldOrders as $o) {
                // Клієнт — якщо counterparty_id є і є в нашому маппінгу
                $clientId = isset($clientMap[$o->counterparty_id])
                    ? $clientMap[$o->counterparty_id]
                    : null;

                if (!$clientId) {
                    $skipped++;
                    $this->command->warn(
                        "   ⚠️  Замовлення #{$o->id} пропущено: немає клієнта (counterparty_id={$o->counterparty_id})"
                    );
                    continue;
                }

                // Визначаємо статус
                $statusCode = $oldStatuses[$o->status_id]?->code ?? 'accepted';
                $newStatus  = $this->statusMap[$statusCode] ?? 'new';

                // Назва моделі пристрою
                $modelName = $o->device_model ?? 'Невідома модель';

                // Знаходимо або створюємо модель пристрою
                $newBrandId      = $brandMap[$o->brand_id] ?? null;
                $newDeviceTypeId = $deviceTypeMap[$o->device_id] ?? null;

                $modelId = null;
                if ($newBrandId && $newDeviceTypeId) {
                    $existingModel = DB::table('models')
                        ->where('brand_id', $newBrandId)
                        ->where('name', $modelName)
                        ->first();

                    if ($existingModel) {
                        $modelId = $existingModel->id;
                    } else {
                        $modelId = DB::table('models')->insertGetId([
                            'brand_id'       => $newBrandId,
                            'device_type_id' => $newDeviceTypeId,
                            'name'           => $modelName,
                            'is_active'      => true,
                            'created_at'     => $o->created_at,
                            'updated_at'     => $o->updated_at,
                        ]);
                    }
                }

                // Створюємо конкретний пристрій
                $deviceId = DB::table('devices')->insertGetId([
                    'device_type_id'  => $newDeviceTypeId,
                    'brand_id'        => $newBrandId,
                    'model_id'        => $modelId,
                    'serial_number'   => $o->serial_number,
                    'imei'            => null,
                    'color'           => null,
                    'appearance'      => $o->equipment, // комплектація → зовнішній вигляд
                    'production_year' => null,
                    'created_at'      => $o->created_at,
                    'updated_at'      => $o->updated_at,
                ]);

                // Генеруємо номер замовлення
                $year   = date('Y', strtotime($o->created_at));
                $number = 'SC-' . $year . '-' . str_pad($o->id, 5, '0', STR_PAD_LEFT);

                // Визначаємо issued_at і cancelled_at
                $issuedAt    = in_array($newStatus, ['issued']) ? $o->updated_at : null;
                $cancelledAt = $newStatus === 'cancelled' ? $o->updated_at : null;

                // Створюємо замовлення
                $newOrderId = DB::table('orders')->insertGetId([
                    'number'          => $number,
                    'client_id'       => $clientId,
                    'device_id'       => $deviceId,
                    'manager_id'      => $adminId,
                    'type'            => 'repair',
                    'priority'        => 'normal',
                    'status'          => $newStatus,
                    'malfunction'     => $o->problem_description,
                    'diagnosis'       => null,
                    'notes'           => null,
                    'check_code'      => str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT),
                    'prepayment'      => 0,
                    'estimated_date'  => null,
                    'issued_at'       => $issuedAt,
                    'cancelled_at'    => $cancelledAt,
                    'cancel_reason'   => null,
                    'created_at'      => $o->created_at,
                    'updated_at'      => $o->updated_at,
                ]);

                $orderMap[$o->id] = $newOrderId;

                // Записуємо початковий статус в історію
                DB::table('order_status_history')->insert([
                    'order_id'    => $newOrderId,
                    'user_id'     => $adminId,
                    'status_from' => null,
                    'status_to'   => $newStatus,
                    'comment'     => 'Імпортовано зі старої системи',
                    'created_at'  => $o->created_at,
                    'updated_at'  => $o->created_at,
                ]);
            }

            $migrated = count($orderMap);
            $this->command->info("   ✅ Перенесено замовлень: {$migrated}, пропущено: {$skipped}");

            // ──────────────────────────────────────────────
            // 5. КОШТОРИСИ (estimates → estimates + estimate_works/parts)
            // ──────────────────────────────────────────────
            $this->command->info('5/7 Міграція кошторисів...');

            // Групуємо старі кошториси по order_id
            $oldEstimates = $old->table('estimates')->get()->groupBy('order_id');
            $estimateCount = 0;

            foreach ($oldEstimates as $oldOrderId => $items) {
                $newOrderId = $orderMap[$oldOrderId] ?? null;
                if (!$newOrderId) continue;

                // Перевіряємо чи вже є кошторис
                $existingEstimate = DB::table('estimates')
                    ->where('order_id', $newOrderId)
                    ->first();

                if ($existingEstimate) continue;

                // Рахуємо підсумки
                $worksTotal = 0;
                $partsTotal = 0;

                foreach ($items as $item) {
                    $worksTotal += (float)($item->labor_cost ?? 0);
                    $partsTotal += (float)($item->part_cost ?? 0);
                }

                $total = $worksTotal + $partsTotal;

                // Створюємо кошторис
                $estimateId = DB::table('estimates')->insertGetId([
                    'order_id'      => $newOrderId,
                    'works_total'   => $worksTotal,
                    'parts_total'   => $partsTotal,
                    'discount'      => 0,
                    'discount_type' => 'fixed',
                    'total'         => $total,
                    'is_approved'   => true,
                    'approved_at'   => $items->first()->created_at,
                    'notes'         => null,
                    'created_at'    => $items->first()->created_at,
                    'updated_at'    => $items->last()->updated_at,
                ]);

                // Кожен рядок старого кошторису:
                // - якщо є labor_cost > 0 → estimate_works
                // - якщо є part_cost > 0 → estimate_parts
                // У старій БД в одному рядку може бути і те, і те
                foreach ($items as $item) {
                    $hasWork = (float)($item->labor_cost ?? 0) > 0;
                    $hasPart = (float)($item->part_cost ?? 0) > 0;

                    if ($hasWork) {
                        DB::table('estimate_works')->insert([
                            'estimate_id'      => $estimateId,
                            'name'             => $item->description,
                            'work_type'        => 'own',
                            'engineer_id'      => null,
                            'subcontractor_id' => null,
                            'price'            => (float)($item->labor_cost ?? 0),
                            'cost'             => 0,
                            'quantity'         => 1,
                            'total'            => (float)($item->labor_cost ?? 0),
                            'is_warranty'      => false,
                            'status'           => 'done',
                            'created_at'       => $item->created_at,
                            'updated_at'       => $item->updated_at,
                        ]);
                    }

                    if ($hasPart) {
                        DB::table('estimate_parts')->insert([
                            'estimate_id' => $estimateId,
                            'part_id'     => null,
                            'name'        => $item->description . ($hasWork ? ' (запчастина)' : ''),
                            'price'       => (float)($item->part_cost ?? 0),
                            'cost'        => 0,
                            'quantity'    => 1,
                            'total'       => (float)($item->part_cost ?? 0),
                            'is_own_part' => false,
                            'created_at'  => $item->created_at,
                            'updated_at'  => $item->updated_at,
                        ]);
                    }

                    // Якщо обидва нулі — записуємо як роботу з нульовою ціною
                    if (!$hasWork && !$hasPart) {
                        DB::table('estimate_works')->insert([
                            'estimate_id'      => $estimateId,
                            'name'             => $item->description,
                            'work_type'        => 'own',
                            'engineer_id'      => null,
                            'subcontractor_id' => null,
                            'price'            => 0,
                            'cost'             => 0,
                            'quantity'         => 1,
                            'total'            => 0,
                            'is_warranty'      => false,
                            'status'           => 'done',
                            'created_at'       => $item->created_at,
                            'updated_at'       => $item->updated_at,
                        ]);
                    }
                }

                $estimateCount++;
            }

            $this->command->info("   ✅ Перенесено кошторисів: {$estimateCount}");

            // ──────────────────────────────────────────────
            // 6. ЖУРНАЛ АКТИВНОСТІ (activities → order_comments)
            // ──────────────────────────────────────────────
            $this->command->info('6/7 Міграція журналу активності...');

            $oldActivities = $old->table('activities')
                ->where('subject_type', 'like', '%Order%')
                ->get();

            $activityCount = 0;

            foreach ($oldActivities as $a) {
                $newOrderId = $orderMap[$a->subject_id] ?? null;
                if (!$newOrderId) continue;

                DB::table('order_comments')->insert([
                    'order_id'    => $newOrderId,
                    'user_id'     => $a->user_id,
                    'body'        => '[Імпорт] ' . ($a->description ?? $a->event),
                    'is_internal' => true,
                    'created_at'  => $a->created_at,
                    'updated_at'  => $a->updated_at,
                ]);

                $activityCount++;
            }

            $this->command->info("   ✅ Перенесено записів активності: {$activityCount}");

            // ──────────────────────────────────────────────
            // 7. ПОСТАЧАЛЬНИКИ (counterparties group 2,3 → suppliers)
            // ──────────────────────────────────────────────
            $this->command->info('7/7 Міграція постачальників і підрядників...');

            $oldSuppliers = $old->table('counterparties')
                ->whereIn('group_id', [2, 3]) // Постачальники та Підрядники
                ->get();

            $supplierCount = 0;

            foreach ($oldSuppliers as $cp) {
                $contact = $old->table('contacts')->find($cp->contact_id);
                if (!$contact) continue;

                $type = $cp->group_id == 3 ? 'subcontractor' : 'local';

                DB::table('suppliers')->insert([
                    'name'           => $contact->name,
                    'type'           => $type,
                    'contact_person' => null,
                    'phone'          => $this->normalizePhone($contact->phone),
                    'email'          => $contact->email,
                    'website'        => null,
                    'notes'          => $contact->notes,
                    'is_active'      => true,
                    'created_at'     => $cp->created_at,
                    'updated_at'     => $cp->updated_at,
                ]);

                $supplierCount++;
            }

            $this->command->info("   ✅ Перенесено постачальників: {$supplierCount}");

        }); // кінець транзакції

        $this->command->info('');
        $this->command->info('=== Міграція завершена успішно! ===');
        $this->command->info('');
        $this->command->warn('Перевір:');
        $this->command->warn('  - Замовлення: php artisan tinker → App\Models\Order::count()');
        $this->command->warn('  - Клієнти:    App\Models\Client::count()');
        $this->command->warn('  - Кошториси:  App\Models\Estimate::count()');
    }

    /**
     * Нормалізація номера телефону до формату +380XXXXXXXXX
     */
    private function normalizePhone(?string $phone): ?string
    {
        if (!$phone) return null;

        // Видаляємо все крім цифр і +
        $clean = preg_replace('/[^\d+]/', '', $phone);

        // Якщо починається з 0 — додаємо +38
        if (str_starts_with($clean, '0')) {
            $clean = '+38' . $clean;
        }

        // Якщо починається з 38 без + — додаємо +
        if (str_starts_with($clean, '38') && !str_starts_with($clean, '+')) {
            $clean = '+' . $clean;
        }

        return $clean;
    }
}
