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
            ->with('success', __('academic_year_created_successfully'));
    }

    public function edit(AcademicYear $academicYear)
    {
        return view('admin.academic-years.edit', compact('academicYear'));
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        // Editing the name, dates, or description must not accidentally unset
        // the currently selected academic year.
        $wasCurrent = (bool) $academicYear->is_current;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', \Illuminate\Validation\Rule::unique('academic_years')->ignore($academicYear->id)],
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
        ]);

        $academicYear->update($validated);

        if ($request->boolean('is_current')) {
            $academicYear->setCurrent();
        } elseif ($wasCurrent) {
            // Keep the existing current state when the checkbox is omitted by
            // the browser or the edit form is submitted without changing it.
            $academicYear->update(['is_current' => true]);
        }

        return redirect()->route('admin.academic-years.index')
            ->with('success', __('academic_year_updated_successfully'));
    }

    public function destroy(AcademicYear $academicYear)
    {
        $academicYear->delete();

        return redirect()->route('admin.academic-years.index')
            ->with('success', __('academic_year_deleted_successfully'));
    }

    public function setCurrent(AcademicYear $academicYear)
    {
        $academicYear->setCurrent();

        return redirect()->route('admin.academic-years.index')
            ->with('success', __('academic_year_set_as_current'));
    }
}
