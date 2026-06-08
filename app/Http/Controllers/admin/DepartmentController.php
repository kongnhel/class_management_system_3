<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\User;
use App\Traits\AuditableTrait;
use App\Traits\FirebaseSyncTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    use AuditableTrait, FirebaseSyncTrait;

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
        $validated = $request->validate([
            'name_km' => 'required|string|max:255|unique:departments',
            'name_en' => 'required|string|max:255|unique:departments',
            'faculty_id' => 'required|exists:faculties,id',
            'head_user_id' => 'nullable|exists:users,id',
        ]);

        $department = Department::create($validated);

        $this->logCreated($department);

        return redirect()->route('admin.manage-departments')->with('success', 'ដេប៉ាតឺម៉ង់ត្រូវបានបង្កើតដោយជោគជ័យ!');
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name_km' => ['required', 'string', 'max:255', Rule::unique('departments')->ignore($department->id)],
            'name_en' => ['required', 'string', 'max:255', Rule::unique('departments')->ignore($department->id)],
            'faculty_id' => 'required|exists:faculties,id',
            'head_user_id' => 'nullable|exists:users,id',
        ]);

        $oldAttributes = $department->attributesToArray();
        $department->update($validated);

        $this->logUpdated($department, $oldAttributes);

        return redirect()->route('admin.manage-departments')->with('success', 'ដេប៉ាតឺម៉ង់ត្រូវបានធ្វើបច្ចុបន្បភាពដោយជោគជ័យ');
    }

    public function destroy(Department $department)
    {
        $deptId = $department->id;
        $deptName = $department->name_km;

        try {
            DB::beginTransaction();

            $oldAttributes = $department->attributesToArray();

            $programs = $department->programs()->get();
            foreach ($programs as $program) {
                $program->courses()->delete();
            }
            $department->programs()->delete();
            $department->delete();

            DB::commit();

            $this->logAction('delete', null, $oldAttributes, null, "Deleted department: {$deptName}");

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
