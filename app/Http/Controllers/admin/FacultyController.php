<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\User;
use App\Traits\AuditableTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FacultyController extends Controller
{
    use AuditableTrait;

    public function index()
    {
        $faculties = Faculty::with(['dean', 'departments'])->paginate(10);

        return view('admin.faculties.index', compact('faculties'));
    }

    public function create()
    {
        $professors = User::where('role', 'professor')->get();

        return view('admin.faculties.create', compact('professors'));
    }

    public function edit(Faculty $faculty)
    {
        $faculty->load('departments');
        $professors = User::where('role', 'professor')->get();

        return view('admin.faculties.edit', compact('faculty', 'professors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_km' => ['required', 'string', 'max:255', Rule::unique('faculties', 'name_km')],
            'name_en' => ['required', 'string', 'max:255', Rule::unique('faculties', 'name_en')],
            'dean_user_id' => 'nullable|exists:users,id',
        ]);

        $faculty = Faculty::create($validated);

        try {
            $this->logCreated($faculty);
        } catch (\Exception $e) {
        }

        return redirect()->route('admin.manage-faculties')->with('success', __('មហាវិទ្យាល័យត្រូវបានបង្កើតដោយជោគជ័យ។'));
    }

    public function update(Request $request, Faculty $faculty)
    {
        $validated = $request->validate([
            'name_km' => ['required', 'string', 'max:255', Rule::unique('faculties')->ignore($faculty->id)],
            'name_en' => ['required', 'string', 'max:255', Rule::unique('faculties')->ignore($faculty->id)],
            'dean_user_id' => 'nullable|exists:users,id',
        ]);

        $oldAttributes = $faculty->attributesToArray();
        $faculty->update($validated);

        try {
            $this->logUpdated($faculty, $oldAttributes);
        } catch (\Exception $e) {
        }

        return redirect()->route('admin.manage-faculties')->with('success', __('មហាវិទ្យាល័យត្រូវបានកែប្រែដោយជោគជ័យ។'));
    }

    public function destroy(Faculty $faculty)
    {
        try {
            DB::beginTransaction();

            $oldAttributes = $faculty->attributesToArray();

            foreach ($faculty->departments as $department) {
                foreach ($department->programs as $program) {
                    $program->courses()->delete();
                }
                $department->programs()->delete();
            }

            $faculty->departments()->delete();
            $faculty->delete();

            DB::commit();

            try {
                $this->logAction('delete', null, $oldAttributes, null, "Deleted faculty: {$faculty->name_km}");
            } catch (\Exception $e) {
            }

            return redirect()->route('admin.manage-faculties')
                ->with('success', __('មហាវិទ្យាល័យនិងទិន្នន័យដែលពាក់ព័ន្ធទាំងអស់ត្រូវបានលុបដោយជោគជ័យ។'));

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('admin.manage-faculties')
                ->with('error', __('មិនអាចលុបមហាវិទ្យាល័យបានទេ៖ មានបញ្ហាមួយបានកើតឡើង។'));
        }
    }
}
