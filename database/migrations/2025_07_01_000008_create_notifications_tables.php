<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Реєстр подій системи
        Schema::create('notification_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_key')->unique()->comment('order_created, status_changed, ready...');
            $table->string('description');
            $table->boolean('notify_client')->default(true);
            $table->boolean('notify_engineer')->default(false);
            $table->boolean('notify_manager')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Шаблони повідомлень (подія + канал)
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('notification_events')->cascadeOnDelete();
            $table->enum('channel', ['sms', 'telegram', 'viber', 'whatsapp', 'push', 'email']);
            $table->enum('audience', ['client', 'engineer', 'manager', 'all_staff']);
            $table->string('subject')->nullable()->comment('Тема для email');
            $table->text('body')->comment('Текст з плейсхолдерами {{order_number}}, {{client_name}}...');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['event_id', 'channel', 'audience']);
        });

        // Канали зв'язку клієнта
        Schema::create('client_contact_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->enum('channel', ['sms', 'telegram', 'viber', 'whatsapp']);
            $table->string('contact')->comment('Номер телефону або username');
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedTinyInteger('sort_order')->default(0)->comment('Пріоритет каналу');
            $table->timestamps();

            $table->unique(['client_id', 'channel']);
        });

        // Журнал відправлених сповіщень
        Schema::create('notification_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->nullable()->constrained('notification_templates')->nullOnDelete();
            $table->enum('channel', ['sms', 'telegram', 'viber', 'whatsapp', 'push', 'email']);
            $table->string('recipient_type')->comment('client або user');
            $table->unsignedBigInteger('recipient_id');
            $table->string('recipient_contact')->comment('Телефон або chat_id');
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['pending', 'sent', 'delivered', 'failed'])->default('pending');
            $table->text('payload')->nullable()->comment('Що фактично відправили');
            $table->text('error')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['recipient_type', 'recipient_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_log');
        Schema::dropIfExists('client_contact_preferences');
        Schema::dropIfExists('notification_templates');
        Schema::dropIfExists('notification_events');
    }
};
