<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\CourseOffering;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $query = Announcement::with('poster', 'courseOffering.course', 'courseOffering.program');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title_km', 'like', "%{$search}%")
                  ->orWhere('title_en', 'like', "%{$search}%")
                  ->orWhere('content_km', 'like', "%{$search}%")
                  ->orWhere('target_role', 'like', "%{$search}%");
            });
        }

        $announcements = $query->latest()->paginate(10)->withQueryString();

        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        $courseOfferings = CourseOffering::with('course', 'program')->whereHas('course')->get();
        $role = ['all', 'student', 'professor', 'admin'];

        return view('admin.announcements.create', compact('courseOfferings', 'role'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title_km' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'content_km' => 'required|string',
            'content_en' => 'nullable|string',
            'target_role' => ['nullable', 'string', Rule::in(['all', 'student', 'professor', 'admin'])],
            'course_offering_id' => 'nullable|exists:course_offerings,id',
        ], [
            'title_km.required' => 'ចំណងជើងជាភាសាខ្មែរត្រូវតែបញ្ចូល។',
            'content_km.required' => 'ខ្លឹមសារជាភាសាខ្មែរត្រូវតែបញ្ចូល។',
            'course_offering_id.exists' => 'ការផ្តល់ជូនវគ្គសិក្សាមិនត្រឹមត្រូវទេ។',
        ]);

        try {
            Announcement::create([
                'poster_user_id' => Auth::id(),
                'title_km' => $request->input('title_km'),
                'title_en' => $request->input('title_en'),
                'content_km' => $request->input('content_km'),
                'content_en' => $request->input('content_en'),
                'target_role' => $request->input('target_role'),
                'course_offering_id' => $request->input('course_offering_id'),
            ]);

            Session::flash('success', 'សេចក្តីប្រកាសត្រូវបានបង្កើតដោយជោគជ័យ!');
        } catch (\Exception $e) {
            Session::flash('error', 'មានបញ្ហាក្នុងការបង្កើតសេចក្តីប្រកាស: '.$e->getMessage());
        }

        return redirect()->route('admin.announcements.index');
    }

    public function edit(Announcement $announcement)
    {
        $courseOfferings = CourseOffering::with('course', 'program')->whereHas('course')->get();
        $role = ['all', 'student', 'professor', 'admin'];

        return view('admin.announcements.edit', compact('announcement', 'courseOfferings', 'role'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $request->validate([
            'title_km' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'content_km' => 'required|string',
            'content_en' => 'nullable|string',
            'target_role' => ['nullable', 'string', Rule::in(['all', 'student', 'professor', 'admin'])],
            'course_offering_id' => 'nullable|exists:course_offerings,id',
        ]);

        try {
            $announcement->update($request->all());
            Session::flash('success', 'សេចក្តីប្រកាសត្រូវបានកែប្រែដោយជោគជ័យ!');
        } catch (\Exception $e) {
            Session::flash('error', 'មានបញ្ហាក្នុងការកែប្រែសេចក្តីប្រកាស: '.$e->getMessage());
        }

        return redirect()->route('admin.announcements.index');
    }

    public function destroy(Announcement $announcement)
    {
        try {
            $announcement->delete();
            Session::flash('success', 'សេចក្តីប្រកាសត្រូវបានលុបដោយជោគជ័យ!');
        } catch (\Exception $e) {
            Session::flash('error', 'មានបញ្ហាក្នុងការលុបសេចក្តីប្រកាស: '.$e->getMessage());
        }

        return redirect()->route('admin.announcements.index');
    }
}
