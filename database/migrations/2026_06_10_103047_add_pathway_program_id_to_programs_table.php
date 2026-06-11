<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->foreignId('pathway_program_id')->nullable()->after('duration_years')->constrained('programs')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropForeign(['pathway_program_id']);
            $table->dropColumn('pathway_program_id');
        });
    }
};
