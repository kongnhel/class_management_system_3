<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Services\ImageKitService;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    protected $imageKitService;

    public function __construct(ImageKitService $imageKitService)
    {
        $this->imageKitService = $imageKitService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search', '');

        $rooms = Room::when($search, function ($query, $search) {
            $query->where('room_number', 'like', "%{$search}%")
                  ->orWhere('location_of_room', 'like', "%{$search}%")
                  ->orWhere('type_of_room', 'like', "%{$search}%");
        })->paginate(12)->withQueryString();

        return view('admin.rooms.index', compact('rooms', 'search'));
    }

    public function create()
    {
        return view('admin.rooms.create');
    }

    public function show(Room $room)
    {
        return view('admin.rooms.show', compact('room'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_number' => 'required|string|unique:rooms,room_number|max:255',
            'capacity' => 'required|integer|min:1',
            'wifi_qr_code' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'location_of_room' => 'nullable|string|max:255',
            'type_of_room' => 'nullable|string|max:255',
        ]);

        $data = $request->except('wifi_qr_code');

        if ($request->hasFile('wifi_qr_code')) {
            $url = $this->imageKitService->uploadWifiQrCode($request->file('wifi_qr_code'));
            if ($url) {
                $data['wifi_qr_code'] = $url;
            } else {
                return back()->withErrors(['wifi_qr_code' => 'ការ Upload ទៅ ImageKit បរាជ័យ។']);
            }
        }

        Room::create($data);

        return redirect()->route('admin.rooms.index')->with('success', 'បន្ទប់ត្រូវបានបង្កើតដោយជោគជ័យ!');
    }

    public function edit(Room $room)
    {
        return view('admin.rooms.edit', compact('room'));
    }

    public function update(Request $request, Room $room)
    {
        $request->validate([
            'room_number' => 'required|string|max:255|unique:rooms,room_number,'.$room->id,
            'capacity' => 'required|integer|min:1',
            'wifi_qr_code' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'location_of_room' => 'nullable|string|max:255',
            'type_of_room' => 'nullable|string|max:255',
        ]);

        $data = $request->except('wifi_qr_code');

        if ($request->hasFile('wifi_qr_code')) {
            $url = $this->imageKitService->uploadWifiQrCode($request->file('wifi_qr_code'));
            if ($url) {
                $data['wifi_qr_code'] = $url;
            } else {
                return back()->withErrors(['wifi_qr_code' => 'ការ Upload ទៅ ImageKit បរាជ័យ។']);
            }
        }

        $room->update($data);

        return redirect()->route('admin.rooms.index')->with('success', 'បន្ទប់ត្រូវបានកែប្រែដោយជោគជ័យ!');
    }

    public function destroy(Room $room)
    {
        $room->delete();

        return redirect()->route('admin.rooms.index')->with('success', 'បន្ទប់ត្រូវបានលុបដោយជោគជ័យ!');
    }
}
