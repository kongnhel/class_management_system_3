<?php

namespace App\Livewire;

use App\Models\Faculty;
use Livewire\Component;
use Livewire\WithPagination;

class FacultyTable extends Component
{
    use WithPagination;

    // ត្រូវតែមានបន្ទាត់នេះដាច់ខាត
    protected $listeners = ['refreshComponent' => '$refresh'];

    public function render()
    {
        return view('livewire.faculty-table', [
            'faculties' => Faculty::with('dean')->paginate(10),
        ]);
    }
}
