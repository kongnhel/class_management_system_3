<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::orderBy('created_at', 'desc')->paginate(10);

        return view('admin.academic-years.index', compact('academicYears'));
    }

    public function create()
    {
        return view('admin.academic-years.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:academic_years',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
        ]);

        $academicYear = AcademicYear::create($validated);

        if ($request->boolean('is_current')) {
            $academicYear->setCurrent();
        }

        return redirect()->route('admin.academic-years.index')
            ->with('success', 'ឆ្នាំសិក្សាត្រូវបានបង្កើតដោយជោគជ័យ។');
    }

    public function edit(AcademicYear $academicYear)
    {
        return view('admin.academic-years.edit', compact('academicYear'));
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', \Illuminate\Validation\Rule::unique('academic_years')->ignore($academicYear->id)],
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
        ]);

        $academicYear->update($validated);

        if ($request->boolean('is_current')) {
            $academicYear->setCurrent();
        }

        return redirect()->route('admin.academic-years.index')
            ->with('success', 'ឆ្នាំសិក្សាត្រូវបានធ្វើបច្ចុប្បន្នដោយជោគជ័យ។');
    }

    public function destroy(AcademicYear $academicYear)
    {
        $academicYear->delete();

        return redirect()->route('admin.academic-years.index')
            ->with('success', 'ឆ្នាំសិក្សាត្រូវបានលុបដោយជោគជ័យ។');
    }

    public function setCurrent(AcademicYear $academicYear)
    {
        $academicYear->setCurrent();

        return redirect()->route('admin.academic-years.index')
            ->with('success', 'ឆ្នាំសិក្សាត្រូវបានកំណត់ជាឆ្នាំសិក្សាបច្ចុប្បន្ន។');
    }
}
