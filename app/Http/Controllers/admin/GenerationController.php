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
            ->with('success', __('generation_created_successfully'));
    }

    public function update(Request $request, Generation $generation)
    {
        $studentCount = $generation->students()->count();

        if ($studentCount > 0) {
            return back()->with('error', __('មិនអាចកែប្រែបានទេ ព្រោះជំនាន់នេះមានសិស្សចំនួន :count នាក់។', ['count' => $studentCount]));
        }

        $request->validate([
            'name' => 'required|integer|min:1|max:99|unique:generations,name,'.$generation->id,
        ]);

        $generation->update([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.generations.index')
            ->with('success', __('generation_updated_successfully'));
    }

    public function destroy(Generation $generation)
    {
        $studentCount = $generation->students()->count();

        if ($studentCount > 0) {
            return back()->with('error', __('មិនអាចលុបបានទេ ព្រោះជំនាន់នេះមានសិស្សចំនួន :count នាក់។', ['count' => $studentCount]));
        }

        $generation->delete();

        return redirect()->route('admin.generations.index')
            ->with('success', __('generation_deleted_successfully'));
    }

    public function toggle(Generation $generation)
    {
        $generation->update(['is_active' => ! $generation->is_active]);

        $status = $generation->is_active ? 'សកម្ម' : 'មិនសកម្ម';

        return redirect()->route('admin.generations.index')
            ->with('success', __('ជំនាន់នេះត្រូវបានកំណត់ជា :status។', ['status' => $status]));
    }
}
