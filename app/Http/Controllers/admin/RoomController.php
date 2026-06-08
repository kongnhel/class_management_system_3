<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Services\ImageKitService;
use App\Traits\FirebaseSyncTrait;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    use FirebaseSyncTrait;

    protected $imageKitService;

    public function __construct(ImageKitService $imageKitService)
    {
        $this->imageKitService = $imageKitService;
    }

    public function index()
    {
        $rooms = Room::paginate(10);

        return view('admin.rooms.index', compact('rooms'));
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

        $room = Room::create($data);

        try {
            $this->syncFirebaseNode('rooms/'.$room->id, [
                'room_number' => $room->room_number,
                'capacity' => $room->capacity,
                'updated_at' => now()->toDateTimeString(),
            ]);
            $this->syncWithFirebase('rooms_sync', 'បន្ទប់លេខ '.$room->room_number.' ត្រូវបានបង្កើតថ្មី!');
        } catch (\Exception $e) {
            \Log::error('Firebase Store Error: '.$e->getMessage());
        }

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

        try {
            $this->updateFirebaseNode('rooms/'.$room->id, [
                'room_number' => $room->room_number,
                'capacity' => $room->capacity,
                'updated_at' => now()->toDateTimeString(),
            ]);
            $this->syncWithFirebase('rooms_sync', 'ទិន្នន័យបន្ទប់លេខ '.$room->room_number.' ត្រូវបានកែប្រែ!');
        } catch (\Exception $e) {
            \Log::error('Firebase Update Error: '.$e->getMessage());
        }

        return redirect()->route('admin.rooms.index')->with('success', 'បន្ទប់ត្រូវបានកែប្រែដោយជោគជ័យ!');
    }

    public function destroy(Room $room)
    {
        $roomNumber = $room->room_number;
        $roomId = $room->id;

        $room->delete();

        try {
            $this->removeFirebaseNode('rooms/'.$roomId);
            $this->syncWithFirebase('rooms_sync', 'បន្ទប់លេខ '.$roomNumber.' ត្រូវបានលុបចេញពីប្រព័ន្ធ!');
        } catch (\Exception $e) {
            \Log::error('Firebase Delete Error: '.$e->getMessage());
        }

        return redirect()->route('admin.rooms.index')->with('success', 'បន្ទប់ត្រូវបានលុបដោយជោគជ័យ!');
    }
}
