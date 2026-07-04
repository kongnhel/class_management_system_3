<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Generation;
use Illuminate\Http\Request;

class GenerationController extends Controller
{
    public function index()
    {
        $generations = Generation::withCount('students')->orderByDesc('name')->get();

        return view('admin.generations.index', compact('generations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|integer|min:1|max:99|unique:generations,name',
        ]);

        Generation::create([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.generations.index')
            ->with('success', 'ជំនាន់ថ្មីបានបង្កើតដោយជោគជ័យ!');
    }

    public function destroy(Generation $generation)
    {
        $studentCount = $generation->students()->count();

        if ($studentCount > 0) {
            return back()->with('error', "មិនអាចលុបបានទេ ព្រោះជំនាន់នេះមានសិស្សចំនួន {$studentCount} នាក់។");
        }

        $generation->delete();

        return redirect()->route('admin.generations.index')
            ->with('success', 'ជំនាន់បានលុបដោយជោគជ័យ!');
    }
}
