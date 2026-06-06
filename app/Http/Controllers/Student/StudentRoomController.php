<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Room;

class StudentRoomController extends Controller
{
    public function rooms()
    {
        $rooms = Room::all();

        return view('student.rooms.index', compact('rooms'));
    }
}
