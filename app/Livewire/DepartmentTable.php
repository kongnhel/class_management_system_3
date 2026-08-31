<?php

namespace App\Livewire;

use App\Models\Department;
use App\Models\Faculty;
use Livewire\Component;
use Livewire\WithPagination;

class DepartmentTable extends Component
{
    use WithPagination;

    public $search = '';
    public $facultyId = '';

    protected $listeners = ['refreshComponent' => '$refresh'];

    protected $queryString = [
        'facultyId' => ['except' => '', 'as' => 'faculty_id'],
    ];

    public function mount()
    {
        $this->facultyId = (string) request()->query('faculty_id', '');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFacultyId()
    {
        $this->resetPage();
    }

    public function render()
    {
        $faculties = Faculty::all();

        $departments = Department::with('faculty', 'head')
            ->when($this->search !== '', function ($query) {
                $query->where(function ($q) {
                    $q->where('name_km', 'like', "%{$this->search}%")
                      ->orWhere('name_en', 'like', "%{$this->search}%")
                      ->orWhereHas('faculty', function ($fq) {
                          $fq->where('name_km', 'like', "%{$this->search}%")
                            ->orWhere('name_en', 'like', "%{$this->search}%");
                      });
                });
            })
            ->when($this->facultyId !== '', function ($query) {
                $query->where('faculty_id', $this->facultyId);
            })
            ->paginate(10);

        return view('livewire.department-table', [
            'departments' => $departments,
            'faculties' => $faculties,
        ]);
    }
}
