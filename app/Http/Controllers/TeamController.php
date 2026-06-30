<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Role;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TeamController extends Controller
{
    public function index()
    {
        $members     = TeamMember::with('department', 'branch', 'systemRole')->get();
        $departments = Department::all();
        $branches    = Branch::orderBy('name')->get();
        $roles       = Role::orderBy('name')->get();

        return view('teams', compact('members', 'departments', 'branches', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $this->validatePayload($request);
        $validated = $this->applyCredentials($validated, $request, true);

        $validated['active_tasks'] = 0;
        $validated['workload_percent'] = 0;
        $validated['status'] = 'active';

        TeamMember::create($validated);

        return redirect()->route('teams')->with('success', 'Đã thêm nhân sự.');
    }

    public function edit(TeamMember $teamMember)
    {
        $departments = Department::all();
        $branches    = Branch::orderBy('name')->get();
        $roles       = Role::orderBy('name')->get();

        return view('teams-edit', compact('teamMember', 'departments', 'branches', 'roles'));
    }

    public function update(Request $request, TeamMember $teamMember)
    {
        $validated = $this->validatePayload($request, $teamMember->id);
        $validated = $this->applyCredentials($validated, $request, false);

        $teamMember->update($validated);

        return redirect()->route('teams')->with('success', 'Đã cập nhật nhân sự.');
    }

    public function destroy(TeamMember $teamMember)
    {
        $teamMember->delete();

        return redirect()->route('teams')->with('success', 'Đã gỡ nhân sự.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:team_members,id']);
        TeamMember::whereIn('id', $request->ids)->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => count($request->ids) . ' nhân sự đã xóa.']);
        }
        return redirect()->route('teams')->with('success', count($request->ids) . ' nhân sự đã xóa.');
    }

    private function validatePayload(Request $request, ?int $ignoreId = null): array
    {
        $emailRule    = 'required|email|unique:team_members,email' . ($ignoreId ? ',' . $ignoreId : '');
        $usernameRule = 'nullable|string|max:80|unique:team_members,username' . ($ignoreId ? ',' . $ignoreId : '');

        return $request->validate([
            'name'          => 'required|string|max:100',
            'email'         => $emailRule,
            'role'          => 'required|string|max:100',
            'position'      => 'nullable|string|max:100',
            'department_id' => 'required|exists:departments,id',
            'branch_id'     => 'nullable|exists:branches,id',
            'role_id'       => 'nullable|exists:roles,id',
            'phone'         => 'nullable|string|max:20',
            'username'      => $usernameRule,
            'password'      => 'nullable|string|min:4|max:200',
        ]);
    }

    private function applyCredentials(array $validated, Request $request, bool $isCreate): array
    {
        $password = $request->input('password');
        if ($password) {
            $validated['password'] = Hash::make($password);
            $validated['must_change_password'] = false;
        } else {
            unset($validated['password']);
        }

        if ($isCreate && empty($validated['username'])) {
            unset($validated['username']);
        }

        return $validated;
    }
}
