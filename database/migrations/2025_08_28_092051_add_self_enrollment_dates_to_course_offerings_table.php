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
        Schema::table('course_offerings', function (Blueprint $table) {
            // 💡 IMPORTANT: ពិនិត្យមើលថាតើ columns ទាំងនេះមានរួចហើយឬនៅ មុនពេលបន្ថែម
            if (! Schema::hasColumn('course_offerings', 'is_open_for_self_enrollment')) {
                $table->boolean('is_open_for_self_enrollment')->default(false)->after('room_number');
            }
            if (! Schema::hasColumn('course_offerings', 'start_date')) {
                $table->date('start_date')->nullable()->after('is_open_for_self_enrollment');
            }
            if (! Schema::hasColumn('course_offerings', 'end_date')) {
                $table->date('end_date')->nullable()->after('start_date');
            }

            // 💡 ពិនិត្យមើល program_id បើវាមិនទាន់មានទេ អ្នកអាចបន្ថែមវាបាន។
            // ប្រសិនបើ program_id ត្រូវបានបន្ថែមរួចហើយក្នុង migration ផ្សេង សូមលុបបន្ទាត់នេះ។
            // if (!Schema::hasColumn('course_offerings', 'program_id')) {
            //     $table->foreignId('program_id')->nullable()->constrained()->onDelete('set null')->after('id');
            // }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_offerings', function (Blueprint $table) {
            // 💡 IMPORTANT: លុប columns វិញដោយពិនិត្យមើលថាតើវាមានឬអត់
            if (Schema::hasColumn('course_offerings', 'is_open_for_self_enrollment')) {
                $table->dropColumn('is_open_for_self_enrollment');
            }
            if (Schema::hasColumn('course_offerings', 'start_date')) {
                $table->dropColumn('start_date');
            }
            if (Schema::hasColumn('course_offerings', 'end_date')) {
                $table->dropColumn('end_date');
            }

            // 💡 ប្រសិនបើអ្នកបានបន្ថែម program_id ខាងលើ សូមលុបវាចេញនៅទីនេះផងដែរ។
            // if (Schema::hasColumn('course_offerings', 'program_id')) {
            //     $table->dropConstrainedForeignId('program_id');
            // }
        });
    }
};
