<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'profile_status')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('profile_status')->default('complete')->after('generation');
                $table->index('profile_status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'profile_status')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex(['profile_status']);
                $table->dropColumn('profile_status');
            });
        }
    }
};
