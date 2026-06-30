<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('otp_code')->nullable()->after('phone');
            $table->timestamp('otp_expires_at')->nullable()->after('otp_code');
            $table->integer('otp_attempts')->default(0)->after('otp_expires_at');
            $table->timestamp('otp_last_sent_at')->nullable()->after('otp_attempts');
            $table->boolean('is_verified')->default(false)->after('otp_last_sent_at');
            $table->string('verification_method')->nullable()->after('is_verified');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'otp_code', 'otp_expires_at', 'otp_attempts',
                'otp_last_sent_at', 'is_verified', 'verification_method',
            ]);
        });
    }
};