<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        // ១. កំណត់ Role ឱ្យត្រូវនឹង Database
        $role = $this->filters['tab'] ?? 'admins';
        $roleMap = ['admins' => 'admin', 'professors' => 'professor', 'students' => 'student'];
        $dbRole = $roleMap[$role] ?? 'admin';

        $query = User::query()->where('role', $dbRole);

        if (! empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhereHas('profile', function ($q2) use ($search) {
                        $q2->where('full_name_km', 'LIKE', "%{$search}%");
                    });
                if ($this->filters['tab'] === 'students') {
                    $q->orWhereHas('studentProfile', function ($q3) use ($search) {
                        $q3->where('full_name_km', 'LIKE', "%{$search}%");
                    });
                }
            });
        }

        if ($dbRole === 'student') {
            if (! empty($this->filters['generation'])) {
                $query->where('generation', $this->filters['generation']);
            }

            if (! empty($this->filters['program_id'])) {
                $query->where('program_id', $this->filters['program_id']);
            }
        }

        return $query->with(['profile', 'studentProfile', 'program', 'department']);
    }

    public function headings(): array
    {
        return [
            'ឈ្មោះអ្នកប្រើ',
            'ឈ្មោះពេញ (ខ្មែរ)',
            'អ៊ីម៉ែល',
            'តួនាទី',
            'ជំនាន់/ជំនាញ ឬ ដេប៉ាតឺម៉ង់',
            'កាលបរិច្ឆេទបង្កើត',
        ];
    }

    public function map($user): array
    {
        $fullName = ($user->role === 'student')
            ? ($user->studentProfile->full_name_km ?? 'N/A')
            : ($user->profile->full_name_km ?? 'N/A');

        $extraInfo = 'N/A';
        if ($user->role === 'student') {
            $gen = $user->generation ? "Gen {$user->generation}" : '';
            $prog = $user->program->name_km ?? 'N/A';
            $extraInfo = "$prog ($gen)";
        } elseif ($user->role === 'professor') {
            $extraInfo = $user->department->name_km ?? 'N/A';
        }

        return [
            $user->name,
            $fullName,
            $user->email,
            ucfirst($user->role),
            $extraInfo,
            $user->created_at ? $user->created_at->format('d-m-Y') : 'N/A',
        ];
    }
}
