<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationEventsSeeder extends Seeder
{
    public function run(): void
    {
        // ──────────────────────────────────────────
        // Реєстр подій
        // ──────────────────────────────────────────
        $events = [
            [
                'key'              => 'order_created',
                'description'      => 'Створення нової заявки',
                'notify_client'    => true,
                'notify_engineer'  => false,
                'notify_manager'   => false,
            ],
            [
                'key'              => 'order_status_changed',
                'description'      => 'Зміна статусу заявки',
                'notify_client'    => true,
                'notify_engineer'  => true,
                'notify_manager'   => false,
            ],
            [
                'key'              => 'order_diagnosed',
                'description'      => 'Діагностика завершена, очікує погодження',
                'notify_client'    => true,
                'notify_engineer'  => false,
                'notify_manager'   => true,
            ],
            [
                'key'              => 'order_ready',
                'description'      => 'Замовлення готове до видачі',
                'notify_client'    => true,
                'notify_engineer'  => false,
                'notify_manager'   => false,
            ],
            [
                'key'              => 'order_issued',
                'description'      => 'Замовлення видане клієнту',
                'notify_client'    => false,
                'notify_engineer'  => false,
                'notify_manager'   => false,
            ],
            [
                'key'              => 'order_cancelled',
                'description'      => 'Замовлення скасоване',
                'notify_client'    => true,
                'notify_engineer'  => true,
                'notify_manager'   => false,
            ],
            [
                'key'              => 'order_waiting_parts',
                'description'      => 'Очікування запчастин',
                'notify_client'    => true,
                'notify_engineer'  => false,
                'notify_manager'   => false,
            ],
            [
                'key'              => 'task_assigned',
                'description'      => 'Інженеру призначено завдання',
                'notify_client'    => false,
                'notify_engineer'  => true,
                'notify_manager'   => false,
            ],
            [
                'key'              => 'task_overdue',
                'description'      => 'Завдання прострочене',
                'notify_client'    => false,
                'notify_engineer'  => true,
                'notify_manager'   => true,
            ],
            [
                'key'              => 'warranty_expiring',
                'description'      => 'Гарантія закінчується через 7 днів',
                'notify_client'    => true,
                'notify_engineer'  => false,
                'notify_manager'   => false,
            ],
            [
                'key'              => 'low_stock',
                'description'      => 'Залишок запчастини нижче мінімуму',
                'notify_client'    => false,
                'notify_engineer'  => false,
                'notify_manager'   => true,
            ],
            [
                'key'              => 'salary_period_ready',
                'description'      => 'Розрахунковий період сформовано',
                'notify_client'    => false,
                'notify_engineer'  => true,
                'notify_manager'   => false,
            ],
        ];

        foreach ($events as $event) {
            DB::table('notification_events')->updateOrInsert(
                ['event_key' => $event['key']],
                [
                    'description'     => $event['description'],
                    'notify_client'   => $event['notify_client'],
                    'notify_engineer' => $event['notify_engineer'],
                    'notify_manager'  => $event['notify_manager'],
                    'is_active'       => true,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]
            );
        }

        $this->command->info('✅ Події сповіщень: ' . count($events));

        // ──────────────────────────────────────────
        // Шаблони повідомлень
        // ──────────────────────────────────────────
        $templates = [
            // Нова заявка
            [
                'event_key' => 'order_created',
                'channel'   => 'sms',
                'audience'  => 'client',
                'body'      => 'Ваша заявка №{{order_number}} прийнята. Перевірочний код: {{check_code}}. Статус: {{status_url}}',
            ],
            [
                'event_key' => 'order_created',
                'channel'   => 'telegram',
                'audience'  => 'client',
                'body'      => "✅ *Заявку прийнято!*\n\nНомер: `{{order_number}}`\nПристрій: {{device_name}}\nОпис проблеми: {{malfunction}}\n\nПеревірити статус: {{status_url}}\nКод доступу: `{{check_code}}`",
            ],

            // Готове до видачі
            [
                'event_key' => 'order_ready',
                'channel'   => 'sms',
                'audience'  => 'client',
                'body'      => '{{device_name}} готовий до видачі. Замовлення №{{order_number}}. Сума: {{total}} грн. {{address}}',
            ],
            [
                'event_key' => 'order_ready',
                'channel'   => 'telegram',
                'audience'  => 'client',
                'body'      => "🎉 *Ваш пристрій готовий!*\n\nЗамовлення: `{{order_number}}`\nПристрій: {{device_name}}\nСума до сплати: *{{total}} грн*\n\nЧекаємо вас за адресою: {{address}}\nГодини роботи: {{working_hours}}",
            ],
            [
                'event_key' => 'order_ready',
                'channel'   => 'viber',
                'audience'  => 'client',
                'body'      => "🎉 Ваш пристрій готовий!\n\nЗамовлення №{{order_number}}\nСума: {{total}} грн\n\nАдреса: {{address}}",
            ],

            // Зміна статусу
            [
                'event_key' => 'order_status_changed',
                'channel'   => 'telegram',
                'audience'  => 'client',
                'body'      => "ℹ️ Статус замовлення №{{order_number}} змінено\n\n{{status_from}} → *{{status_to}}*\n\n{{comment}}\n\nПеревірити: {{status_url}}",
            ],
            [
                'event_key' => 'order_status_changed',
                'channel'   => 'sms',
                'audience'  => 'client',
                'body'      => 'Замовлення №{{order_number}}: статус змінено на "{{status_to}}". Деталі: {{status_url}}',
            ],

            // Очікування запчастин
            [
                'event_key' => 'order_waiting_parts',
                'channel'   => 'telegram',
                'audience'  => 'client',
                'body'      => "⏳ *Очікуємо запчастину*\n\nЗамовлення №{{order_number}}\nОчікувана дата готовності: {{estimated_date}}\n\nМи повідомимо вас коли пристрій буде готовий.",
            ],

            // Призначення завдання інженеру
            [
                'event_key' => 'task_assigned',
                'channel'   => 'telegram',
                'audience'  => 'engineer',
                'body'      => "🔧 *Нове завдання*\n\nЗаявка: №{{order_number}}\nПристрій: {{device_name}}\nЗавдання: {{task_title}}\n{{task_description}}\n\nТермін: {{due_at}}",
            ],
            [
                'event_key' => 'task_assigned',
                'channel'   => 'push',
                'audience'  => 'engineer',
                'body'      => 'Нове завдання: {{task_title}} (Заявка №{{order_number}})',
            ],

            // Прострочене завдання
            [
                'event_key' => 'task_overdue',
                'channel'   => 'telegram',
                'audience'  => 'engineer',
                'body'      => "⚠️ *Завдання прострочено*\n\nЗаявка: №{{order_number}}\nЗавдання: {{task_title}}\nТермін був: {{due_at}}",
            ],
            [
                'event_key' => 'task_overdue',
                'channel'   => 'telegram',
                'audience'  => 'manager',
                'body'      => "⚠️ *Прострочене завдання*\n\nІнженер: {{engineer_name}}\nЗаявка: №{{order_number}}\nЗавдання: {{task_title}}\nТермін був: {{due_at}}",
            ],

            // Мінімальний залишок
            [
                'event_key' => 'low_stock',
                'channel'   => 'telegram',
                'audience'  => 'manager',
                'body'      => "📦 *Мінімальний залишок*\n\nЗапчастина: {{part_name}}\nПоточний залишок: *{{stock_qty}} {{unit}}*\nМінімум: {{min_stock_qty}} {{unit}}\n\nРекомендуємо замовити у: {{preferred_supplier}}",
            ],

            // Скасування
            [
                'event_key' => 'order_cancelled',
                'channel'   => 'telegram',
                'audience'  => 'client',
                'body'      => "❌ *Замовлення скасовано*\n\nЗамовлення №{{order_number}}\nПричина: {{cancel_reason}}\n\nЗ питань звертайтесь: {{phone}}",
            ],
        ];

        $eventIds = DB::table('notification_events')->pluck('id', 'event_key');

        foreach ($templates as $template) {
            $eventId = $eventIds[$template['event_key']] ?? null;
            if (!$eventId) continue;

            DB::table('notification_templates')->updateOrInsert(
                [
                    'event_id' => $eventId,
                    'channel'  => $template['channel'],
                    'audience' => $template['audience'],
                ],
                [
                    'subject'    => $template['subject'] ?? null,
                    'body'       => $template['body'],
                    'is_active'  => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('✅ Шаблони сповіщень: ' . count($templates));
        $this->command->info('');
        $this->command->info('Плейсхолдери у шаблонах:');
        $this->command->line('  {{order_number}}, {{check_code}}, {{device_name}}');
        $this->command->line('  {{status_from}}, {{status_to}}, {{status_url}}');
        $this->command->line('  {{total}}, {{estimated_date}}, {{address}}');
        $this->command->line('  {{task_title}}, {{due_at}}, {{engineer_name}}');
        $this->command->line('  {{part_name}}, {{stock_qty}}, {{preferred_supplier}}');
    }
}
