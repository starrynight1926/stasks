<?php

namespace App\Http\Controllers;

use App\Models\TeamMember;
use App\Models\Department;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index()
    {
        $members = TeamMember::with('department')->get();
        $departments = Department::all();

        return view('teams', compact('members', 'departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:team_members,email',
            'role' => 'required|string|max:100',
            'position' => 'nullable|string|max:100',
            'department_id' => 'required|exists:departments,id',
            'phone' => 'nullable|string|max:20',
        ]);

        $validated['active_tasks'] = 0;
        $validated['workload_percent'] = 0;
        $validated['status'] = 'active';

        TeamMember::create($validated);

        return redirect()->route('teams')->with('success', 'Member added successfully.');
    }

    public function edit(TeamMember $teamMember)
    {
        $departments = Department::all();

        return view('teams-edit', compact('teamMember', 'departments'));
    }

    public function update(Request $request, TeamMember $teamMember)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:team_members,email,' . $teamMember->id,
            'role' => 'required|string|max:100',
            'position' => 'nullable|string|max:100',
            'department_id' => 'required|exists:departments,id',
            'phone' => 'nullable|string|max:20',
        ]);

        $teamMember->update($validated);

        return redirect()->route('teams')->with('success', 'Member updated successfully.');
    }

    public function destroy(TeamMember $teamMember)
    {
        $teamMember->delete();

        return redirect()->route('teams')->with('success', 'Member removed successfully.');
    }
}
