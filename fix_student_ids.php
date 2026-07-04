<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Services\StudentIdGeneratorService;
use Illuminate\Support\Facades\DB;

echo "DB: " . config('database.default') . "\n";
echo "Database: " . DB::connection()->getDatabaseName() . "\n\n";

$generator = new StudentIdGeneratorService();

$students = User::where('role', 'student')
    ->whereNotNull('program_id')
    ->whereNotNull('generation')
    ->orderBy('id')
    ->get();

$count = 0;

foreach ($students as $student) {
    $newId = $generator->generate($student->program_id, $student->generation);
    
    while (User::where('student_id_code', $newId)->where('id', '!=', $student->id)->exists()) {
        $parts = explode('-', $newId);
        $nextSerial = (int) end($parts) + 1;
        $parts[2] = str_pad($nextSerial, 6, '0', STR_PAD_LEFT);
        $newId = implode('-', $parts);
    }
    
    $oldId = $student->student_id_code;
    $student->student_id_code = $newId;
    $student->save();
    $count++;
    
    echo "Updated: {$student->name} | {$oldId} → {$newId}\n";
}

echo "\nDone: {$count} students updated.\n";
