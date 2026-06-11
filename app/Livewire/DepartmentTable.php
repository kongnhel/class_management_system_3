<?php

namespace App\Livewire;

use App\Models\Department;
use Livewire\Component;
use Livewire\WithPagination;

class DepartmentTable extends Component
{
    use WithPagination;

    public $search = '';

    protected $listeners = ['refreshComponent' => '$refresh'];

    protected $queryString = ['search' => ['except' => '']];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $departments = Department::with('faculty', 'head')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name_km', 'like', "%{$this->search}%")
                      ->orWhere('name_en', 'like', "%{$this->search}%")
                      ->orWhereHas('faculty', function ($fq) {
                          $fq->where('name_km', 'like', "%{$this->search}%")
                            ->orWhere('name_en', 'like', "%{$this->search}%");
                      });
                });
            })
            ->paginate(10);

        return view('livewire.department-table', [
            'departments' => $departments,
        ]);
    }
}
