<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * រត់ Migrations.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary(); // UUID សម្រាប់ Notification ID
            $table->string('type'); // ប្រភេទ Notification (ឧ. 'App\Notifications\InvoicePaid')
            $table->morphs('notifiable'); // នេះជាការកែតម្រូវ - វានឹងបង្កើត notifiable_id និង notifiable_type
            $table->text('data'); // ទិន្នន័យ Notification (ជា JSON)
            $table->timestamp('read_at')->nullable(); // កាលបរិច្ឆេទបានអាន
            $table->timestamps(); // created_at និង updated_at
        });
    }

    /**
     * បញ្ច្រាស Migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
