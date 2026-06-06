<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Kreait\Firebase\Factory;

class DepartmentController extends Controller
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

    private function syncWithFirebase($message = 'ទិន្នន័យដេប៉ាតឺម៉ង់ត្រូវបានកែប្រែ')
    {
        try {
            $this->getFirebaseDatabase()
                ->getReference('departments_sync')
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
        $departments = Department::with('faculty', 'head')->paginate(10);

        return view('admin.departments.index', compact('departments'));
    }

    public function create()
    {
        $faculties = Faculty::all();
        $professors = User::where('role', 'professor')->get();

        return view('admin.departments.create', compact('faculties', 'professors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_km' => 'required|string|max:255|unique:departments',
            'name_en' => 'required|string|max:255|unique:departments',
            'faculty_id' => 'required|exists:faculties,id',
            'head_user_id' => 'nullable|exists:users,id',
        ]);

        $department = Department::create($request->all());

        try {
            $db = $this->getFirebaseDatabase();
            $db->getReference('departments/'.$department->id)->set([
                'name_km' => $department->name_km,
                'name_en' => $department->name_en,
                'updated_at' => now()->toDateTimeString(),
            ]);

            $this->syncWithFirebase("ដេប៉ាតឺម៉ង់ '".$department->name_km."' ត្រូវបានបង្កើតថ្មី!");
        } catch (\Exception $e) {
            Log::error('Firebase Store Error: '.$e->getMessage());
        }

        return redirect()->route('admin.manage-departments')->with('success', 'ដេប៉ាតឺម៉ង់ត្រូវបានបង្កើតដោយជោគជ័យ!');
    }

    public function edit(Department $department)
    {
        $faculties = Faculty::all();
        $professors = User::where('role', 'professor')->get();

        return view('admin.departments.edit', compact('department', 'faculties', 'professors'));
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name_km' => ['required', 'string', 'max:255', Rule::unique('departments')->ignore($department->id)],
            'name_en' => ['required', 'string', 'max:255', Rule::unique('departments')->ignore($department->id)],
            'faculty_id' => 'required|exists:faculties,id',
            'head_user_id' => 'nullable|exists:users,id',
        ]);

        $department->update($request->all());

        try {
            $this->getFirebaseDatabase()
                ->getReference('departments/'.$department->id)
                ->update([
                    'name_km' => $department->name_km,
                    'name_en' => $department->name_en,
                    'updated_at' => now()->toDateTimeString(),
                ]);

            $this->syncWithFirebase("ដេប៉ាតឺម៉ង់ '".$department->name_km."' ត្រូវបានធ្វើបច្ចុប្បន្នភាព!");
        } catch (\Exception $e) {
            Log::error('Firebase Update Error: '.$e->getMessage());
        }

        return redirect()->route('admin.manage-departments')->with('success', 'ដេប៉ាតឺម៉ង់ត្រូវបានធ្វើបច្ចុបន្បភាពដោយជោគជ័យ');
    }

    public function destroy(Department $department)
    {
        $deptId = $department->id;
        $deptName = $department->name_km;

        try {
            DB::beginTransaction();

            $programs = $department->programs()->get();
            foreach ($programs as $program) {
                $program->courses()->delete();
            }
            $department->programs()->delete();
            $department->delete();

            DB::commit();

            try {
                $this->getFirebaseDatabase()->getReference('departments/'.$deptId)->remove();
                $this->syncWithFirebase("ដេប៉ាតឺម៉ង់ '".$deptName."' ត្រូវបានលុបចេញពីប្រព័ន្ធ!");
            } catch (\Exception $e) {
                Log::error('Firebase Delete Error: '.$e->getMessage());
            }

            return redirect()->route('admin.manage-departments')->with('success', 'ដេប៉ាតឺម៉ង់ត្រូវបានលុបដោយជោគជ័យ។');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting department: '.$e->getMessage());

            return redirect()->route('admin.manage-departments')->with('error', 'មិនអាចលុបដេប៉ាតឺម៉ង់បានទេ');
        }
    }

    public function getDepartmentsByFaculty($facultyId)
    {
        $departments = Department::where('faculty_id', $facultyId)
            ->select('id', 'name_km', 'name_en')
            ->get();

        return response()->json($departments);
    }
}
