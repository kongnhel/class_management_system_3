<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;

class RoomController extends Controller
{
    private function getFirebaseDatabase()
    {
        $credentialPath = storage_path('app/firebase/classmanagementsystem.json');

        if (! is_file($credentialPath)) {
            throw new \Exception('រកមិនឃើញ File JSON របស់ Firebase ទេ។');
        }

        return (new Factory)
            ->withServiceAccount($credentialPath)
            ->withDatabaseUri('https://classmanagementsystem-cd57f-default-rtdb.firebaseio.com/')
            ->createDatabase();
    }

    private function syncWithFirebase($message = 'ទិន្នន័យបន្ទប់ត្រូវបានធ្វើបច្ចុប្បន្នភាព')
    {
        try {
            $this->getFirebaseDatabase()
                ->getReference('rooms_sync')
                ->set([
                    'updated_at' => now()->timestamp,
                    'message' => $message,
                ]);
        } catch (\Exception $e) {
            Log::error('Firebase Sync Error: '.$e->getMessage());
        }
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
            $file = $request->file('wifi_qr_code');

            $response = Http::withBasicAuth(env('IMAGEKIT_PRIVATE_KEY'), '') // ប្រើ Private Key ជា Username
                ->attach(
                    'file',
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                )
                ->post('https://upload.imagekit.io/api/v1/files/upload', [
                    'fileName' => 'wifi_qr_'.time(),
                    'useUniqueFileName' => 'true',
                    'folder' => '/room_wifi',
                ]);

            if ($response->successful()) {
                $data['wifi_qr_code'] = $response->json()['url'];
            } else {
                return back()->withErrors(['wifi_qr_code' => 'ការ Upload ទៅ ImageKit បរាជ័យ៖ '.$response->body()]);
            }
        }

        $room = Room::create($data);

        try {
            $this->getFirebaseDatabase()->getReference('rooms/'.$room->id)->set([
                'room_number' => $room->room_number,
                'capacity' => $room->capacity,
                'updated_at' => now()->toDateTimeString(),
            ]);
            $this->syncWithFirebase('បន្ទប់លេខ '.$room->room_number.' ត្រូវបានបង្កើតថ្មី!');
        } catch (\Exception $e) {
            Log::error('Firebase Store Error: '.$e->getMessage());
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
            $file = $request->file('wifi_qr_code');

            $response = Http::withBasicAuth(env('IMAGEKIT_PRIVATE_KEY'), '')
                ->attach(
                    'file',
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                )
                ->post('https://upload.imagekit.io/api/v1/files/upload', [
                    'fileName' => 'wifi_qr_'.time(),
                    'useUniqueFileName' => 'true',
                    'folder' => '/room_wifi',
                ]);

            if ($response->successful()) {

                $data['wifi_qr_code'] = $response->json()['url'];
            } else {
                return back()->withErrors(['wifi_qr_code' => 'ការ Upload ទៅ ImageKit បរាជ័យ៖ '.$response->body()]);
            }
        }

        $room->update($data);

        try {
            $this->getFirebaseDatabase()->getReference('rooms/'.$room->id)->update([
                'room_number' => $room->room_number,
                'capacity' => $room->capacity,
                'updated_at' => now()->toDateTimeString(),
            ]);
            $this->syncWithFirebase('ទិន្នន័យបន្ទប់លេខ '.$room->room_number.' ត្រូវបានកែប្រែ!');
        } catch (\Exception $e) {
            Log::error('Firebase Update Error: '.$e->getMessage());
        }

        return redirect()->route('admin.rooms.index')->with('success', 'បន្ទប់ត្រូវបានកែប្រែដោយជោគជ័យ!');
    }

    public function destroy(Room $room)
    {
        $roomNumber = $room->room_number;
        $roomId = $room->id;

        $room->delete();

        try {
            $this->getFirebaseDatabase()->getReference('rooms/'.$roomId)->remove();
            $this->syncWithFirebase('បន្ទប់លេខ '.$roomNumber.' ត្រូវបានលុបចេញពីប្រព័ន្ធ!');
        } catch (\Exception $e) {
            Log::error('Firebase Delete Error: '.$e->getMessage());
        }

        return redirect()->route('admin.rooms.index')->with('success', 'បន្ទប់ត្រូវបានលុបដោយជោគជ័យ!');
    }
}
