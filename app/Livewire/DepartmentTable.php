<?php

namespace App\Livewire;

use App\Models\Department;
use Livewire\Component;
use Livewire\WithPagination;

class DepartmentTable extends Component
{
    use WithPagination;

    // ត្រូវតែមានបន្ទាត់នេះដាច់ខាត
    protected $listeners = ['refreshComponent' => '$refresh'];

    public function render()
    {
        return view('livewire.department-table', [
            'departments' => Department::paginate(10),
        ]);
    }
}
