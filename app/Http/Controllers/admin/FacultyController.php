<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Kreait\Firebase\Factory;

class FacultyController extends Controller
{
    private function getFirebaseDatabase()
    {
        $credentialPath = storage_path('app/firebase/classmanagementsystem.json');

        if (! is_file($credentialPath)) {
            throw new \Exception('Path ខាងលើមិនមែនជាឯកសារ JSON ទេ។ សូមពិនិត្យមើលក្នុង Folder storage/app/firebase។');
        }

        $factory = (new Factory)
            ->withServiceAccount($credentialPath)
            ->withDatabaseUri('https://classmanagementsystem-cd57f-default-rtdb.firebaseio.com/');

        return $factory->createDatabase();
    }

    private function syncWithFirebase($message = 'ទិន្នន័យត្រូវបានកែប្រែ')
    {
        try {
            $this->getFirebaseDatabase()
                ->getReference('faculties_sync')
                ->set([
                    'updated_at' => now()->timestamp,
                    'message' => $message,
                ]);
        } catch (\Exception $e) {
            Log::error('Firebase Error: '.$e->getMessage());
        }
    }

    public function index()
    {
        $faculties = Faculty::with('dean')->paginate(10);

        return view('admin.faculties.index', compact('faculties'));
    }

    public function create()
    {
        $professors = User::where('role', 'professor')->get();

        return view('admin.faculties.create', compact('professors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_km' => ['required', 'string', 'max:255', Rule::unique('faculties', 'name_km')],
            'name_en' => ['required', 'string', 'max:255', Rule::unique('faculties', 'name_en')],
            'dean_user_id' => 'nullable|exists:users,id',
        ]);

        Faculty::create($request->all());

        $this->syncWithFirebase('មហាវិទ្យាល័យថ្មីត្រូវបានបន្ថែម');

        return redirect()->route('admin.manage-faculties')->with('success', 'មហាវិទ្យាល័យត្រូវបានបង្កើតដោយជោគជ័យ។');
    }

    public function edit(Faculty $faculty)
    {
        $professors = User::where('role', 'professor')->get();

        return view('admin.faculties.edit', compact('faculty', 'professors'));
    }

    public function update(Request $request, Faculty $faculty)
    {
        $request->validate([
            'name_km' => ['required', 'string', 'max:255', Rule::unique('faculties')->ignore($faculty->id)],
            'name_en' => ['required', 'string', 'max:255', Rule::unique('faculties')->ignore($faculty->id)],
            'dean_user_id' => 'nullable|exists:users,id',
        ]);

        $faculty->update($request->all());

        $this->syncWithFirebase("មហាវិទ្យាល័យ '{$faculty->name_km}' ត្រូវបានកែប្រែ");

        return redirect()->route('admin.manage-faculties')->with('success', 'មហាវិទ្យាល័យត្រូវបានធ្វើបច្ចុប្បន្នដោយជោគជ័យ!');
    }

    public function destroy(Faculty $faculty)
    {
        try {
            DB::beginTransaction();

            foreach ($faculty->departments as $department) {
                foreach ($department->programs as $program) {
                    $program->courses()->delete();
                }
                $department->programs()->delete();
            }

            $faculty->departments()->delete();
            $faculty->delete();

            DB::commit();

            $this->syncWithFirebase('មហាវិទ្យាល័យមួយត្រូវបានលុបចេញពីប្រព័ន្ធ');

            return redirect()->route('admin.manage-faculties')
                ->with('success', 'មហាវិទ្យាល័យនិងទិន្នន័យដែលពាក់ព័ន្ធទាំងអស់ត្រូវបានលុបដោយជោគជ័យ។');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting faculty: '.$e->getMessage());

            return redirect()->route('admin.manage-faculties')
                ->with('error', 'មិនអាចលុបមហាវិទ្យាល័យបានទេ៖ មានបញ្ហាមួយបានកើតឡើង។');
        }
    }
}
