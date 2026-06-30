<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::with('branch')->withCount('members')->get();
        $branches    = Branch::orderBy('name')->get();

        return view('departments', compact('departments', 'branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|unique:departments,code',
            'color' => 'required|string|max:7',
            'description' => 'nullable|string|max:500',
            'head_name' => 'nullable|string|max:100',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $validated['member_count'] = 0;
        $validated['active_projects'] = 0;
        $validated['performance_score'] = 0;

        Department::create($validated);

        return redirect()->route('departments')->with('success', 'Department created successfully.');
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|unique:departments,code,' . $department->id,
            'color' => 'required|string|max:7',
            'description' => 'nullable|string|max:500',
            'head_name' => 'nullable|string|max:100',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $department->update($validated);

        return redirect()->route('departments')->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        $department->delete();

        return redirect()->route('departments')->with('success', 'Department deleted successfully.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:departments,id']);
        Department::whereIn('id', $request->ids)->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => count($request->ids) . ' departments deleted.']);
        }
        return redirect()->route('departments')->with('success', count($request->ids) . ' departments deleted.');
    }
}
