<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\User;
use App\Traits\AuditableTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    use AuditableTrait;

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

        try {
            $this->logCreated($department);
        } catch (\Exception $e) {}

        return redirect()->route('admin.manage-departments')->with('success', __('ដេប៉ាតឺម៉ង់ត្រូវបានបង្កើតដោយជោគជ័យ។'));
    }

    public function edit(Department $department)
    {
        $department->load('faculty', 'head');
        $faculties = Faculty::all();
        $professors = User::where('role', 'professor')->get();

        return view('admin.departments.edit', compact('department', 'faculties', 'professors'));
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

        try {
            $this->logUpdated($department, $oldAttributes);
        } catch (\Exception $e) {}

        return redirect()->route('admin.manage-departments')->with('success', __('ដេប៉ាតឺម៉ង់ត្រូវបានកែប្រែដោយជោគជ័យ។'));
    }

    public function destroy(Department $department)
    {
        $deptId = $department->id;
        $deptName = $department->name_km;

        if (User::where('department_id', $department->id)->exists()) {
            return redirect()->route('admin.manage-departments')->with('error', __('មិនអាចលុបដេប៉ាតឺម៉ង់នេះបានទេ ព្រោះមានអ្នកប្រើប្រាស់ភ្ជាប់នឹងដេប៉ាតឺម៉ង់នេះ។'));
        }

        if ($department->courses()->withTrashed()->exists()) {
            return redirect()->route('admin.manage-departments')->with('error', __('មិនអាចលុបដេប៉ាតឺម៉ង់នេះបានទេ ព្រោះមានមុខវិជ្ជាភ្ជាប់នឹងដេប៉ាតឺម៉ង់នេះ។'));
        }

        if ($department->programs()->exists()) {
            return redirect()->route('admin.manage-departments')->with('error', __('មិនអាចលុបដេប៉ាតឺម៉ង់នេះបានទេ ព្រោះមានកម្មវិធីសិក្សាភ្ជាប់នឹងដេប៉ាតឺម៉ង់នេះ។'));
        }

        try {
            DB::beginTransaction();

            $oldAttributes = $department->attributesToArray();

            $department->courses()->withTrashed()->forceDelete();

            $programs = $department->programs()->get();
            foreach ($programs as $program) {
                $program->courses()->withTrashed()->forceDelete();
            }
            $department->programs()->delete();
            $department->delete();

            DB::commit();

            try {
                $this->logAction('delete', null, $oldAttributes, null, "Deleted department: {$deptName}");
            } catch (\Exception $e) {}

            return redirect()->route('admin.manage-departments')->with('success', __('ដេប៉ាតឺម៉ង់ត្រូវបានលុបដោយជោគជ័យ។'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting department: '.$e->getMessage());

            return redirect()->route('admin.manage-departments')->with('error', __('មិនអាចលុបដេប៉ាតឺម៉ង់បានទេ'));
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
