<?php

namespace App\Livewire;

use App\Models\Room;
use Livewire\Component;
use Livewire\WithPagination;

class RoomTable extends Component
{
    use WithPagination;

    // ត្រូវតែមានបន្ទាត់នេះដាច់ខាត
    protected $listeners = ['refreshComponent' => '$refresh'];

    // ក្នុង RoomTable.php ត្រូវសរសេរត្រឹមតែ៖
    public function render()
    {
        return view('livewire.room-table', [
            'rooms' => Room::paginate(10),
        ]);
    }
}
