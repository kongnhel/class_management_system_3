<?php

namespace App\Livewire;

use App\Models\Faculty;
use Livewire\Component;
use Livewire\WithPagination;

class FacultyTable extends Component
{
    use WithPagination;

    public $search = '';

    protected $listeners = ['refreshComponent' => '$refresh'];

    protected $queryString = ['search' => ['except' => '']];

    public function mount()
    {
        $this->search = trim((string) request()->query('search', ''));
    }

    public function updatedSearch()
    {
        $this->search = trim($this->search);
        $this->resetPage();
    }

    public function render()
    {
        $query = Faculty::with(['dean', 'departments']);

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name_km', 'like', "%{$this->search}%")
                    ->orWhere('name_en', 'like', "%{$this->search}%");
            });
        }

        return view('livewire.faculty-table', [
            'faculties' => $query->paginate(10),
        ]);
    }
}
