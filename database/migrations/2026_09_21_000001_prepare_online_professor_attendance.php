<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('attendance_professors')) {
            Schema::table('attendance_professors', function (Blueprint $table) {
                if (Schema::hasColumn('attendance_professors', 'lat')) {
                    $table->decimal('lat', 10, 8)->nullable()->change();
                }
                if (Schema::hasColumn('attendance_professors', 'lng')) {
                    $table->decimal('lng', 11, 8)->nullable()->change();
                }
            });
        }

        if (Schema::hasTable('attendance_sessions')) {
            Schema::table('attendance_sessions', function (Blueprint $table) {
                if (! Schema::hasColumn('attendance_sessions', 'online_token_hash')) {
                    $table->string('online_token_hash', 64)->nullable()->after('closed_at');
                }
                if (! Schema::hasColumn('attendance_sessions', 'online_token_expires_at')) {
                    $table->timestamp('online_token_expires_at')->nullable()->after('online_token_hash');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('attendance_sessions')) {
            Schema::table('attendance_sessions', function (Blueprint $table) {
                if (Schema::hasColumn('attendance_sessions', 'online_token_expires_at')) {
                    $table->dropColumn('online_token_expires_at');
                }
                if (Schema::hasColumn('attendance_sessions', 'online_token_hash')) {
                    $table->dropColumn('online_token_hash');
                }
            });
        }

        if (Schema::hasTable('attendance_professors')) {
            Schema::table('attendance_professors', function (Blueprint $table) {
                if (Schema::hasColumn('attendance_professors', 'lat')) {
                    $table->decimal('lat', 10, 8)->nullable(false)->change();
                }
                if (Schema::hasColumn('attendance_professors', 'lng')) {
                    $table->decimal('lng', 11, 8)->nullable(false)->change();
                }
            });
        }
    }
};
