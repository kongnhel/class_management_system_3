<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            // បន្ថែម telegram_user (Username) និង telegram_chat_id (សម្រាប់ Bot ផ្ញើសារ)
            if (! Schema::hasColumn('user_profiles', 'telegram_user')) {
                $table->string('telegram_user')->nullable()->after('phone_number');
            }

            if (! Schema::hasColumn('user_profiles', 'telegram_chat_id')) {
                $table->string('telegram_chat_id')->nullable()->after('telegram_user');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn(['telegram_user', 'telegram_chat_id']);
        });
    }
};
