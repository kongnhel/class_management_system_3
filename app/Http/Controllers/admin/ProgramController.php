<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProgramController extends Controller
{
    public function index(Request $request)
    {
        $query = Program::with('department');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name_km', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%")
                  ->orWhere('degree_level', 'like', "%{$search}%");
            });
        }

        if ($departmentId = $request->input('department_id')) {
            $query->where('department_id', $departmentId);
        }

        if ($degreeLevel = $request->input('degree_level')) {
            $query->where('degree_level', $degreeLevel);
        }

        $sort = $request->input('sort', 'name_km');
        $direction = $request->input('direction', 'asc');
        $allowedSorts = ['name_km', 'name_en', 'duration_years', 'degree_level', 'created_at'];

        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');
        }

        $programs = $query->paginate(12)->withQueryString();
        $departments = Department::all();
        $degreeLevels = Program::distinct()->pluck('degree_level')->filter()->values();

        return view('admin.programs.index', compact('programs', 'departments', 'degreeLevels'));
    }

    public function create()
    {
        $departments = Department::all();
        $programs = Program::where('duration_years', '<=', 2)->get();

        return view('admin.programs.create', compact('departments', 'programs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_km' => 'required|string|max:255|unique:programs',
            'name_en' => 'required|string|max:255|unique:programs',
            'department_id' => 'required|exists:departments,id',
            'duration_years' => 'required|integer|min:1',
            'degree_level' => 'nullable|string|max:50',
            'pathway_program_id' => 'nullable|exists:programs,id',
        ]);

        Program::create($request->except('pathway_program_id') + [
            'pathway_program_id' => $request->pathway_program_id ?: null,
        ]);

        return redirect()->route('admin.manage-programs')->with('success', 'កម្មវិធីសិក្សាបង្កើតដោយជោគជ័យ!');
    }

    public function show(string $id) {}

    public function edit(Program $program)
    {
        $departments = Department::all();
        $programs = Program::where('id', '!=', $program->id)->where('duration_years', '<=', 2)->get();

        return view('admin.programs.edit', compact('program', 'departments', 'programs'));
    }

    public function update(Request $request, Program $program)
    {
        $request->validate([
            'name_km' => ['required', 'string', 'max:255', Rule::unique('programs')->ignore($program->id)],
            'name_en' => ['required', 'string', 'max:255', Rule::unique('programs')->ignore($program->id)],
            'department_id' => 'required|exists:departments,id',
            'duration_years' => 'required|integer|min:1',
            'degree_level' => 'nullable|string|max:50',
            'pathway_program_id' => 'nullable|exists:programs,id',
        ]);

        $program->update($request->except('pathway_program_id') + [
            'pathway_program_id' => $request->pathway_program_id ?: null,
        ]);

        return redirect()->route('admin.manage-programs')->with('success', 'កម្មវិធីសិក្សាត្រូវបានធ្វើបច្ចុប្បន្នភាពដោយជោគជ័យ!');
    }

    public function destroy(Program $program)
    {
        try {
            if ($program->users()->exists()) {
                return redirect()->route('admin.manage-programs')
                    ->with('error', 'មិនអាចលុបកម្មវិធីសិក្សានេះបានទេ ព្រោះមានអ្នកប្រើប្រាស់ដែលពាក់ព័ន្ធ។ សូមផ្ទេរអ្នកប្រើប្រាស់ទាំងនោះទៅកម្មវិធីផ្សេងមុន។');
            }

            $program->delete();

            return redirect()->route('admin.manage-programs')
                ->with('success', 'កម្មវិធីសិក្សាត្រូវបានលុបដោយជោគជ័យ!');
        } catch (\Exception $e) {
            return redirect()->route('admin.manage-programs')
                ->with('error', 'មានកំហុសកើតឡើងក្នុងការលុបកម្មវិធីសិក្សា។ សូមព្យាយាមម្តងទៀត។');
        }
    }
}
