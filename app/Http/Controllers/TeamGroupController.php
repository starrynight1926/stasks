<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Team;
use App\Models\TeamMember;
use Illuminate\Http\Request;

class TeamGroupController extends Controller
{
    public function index()
    {
        $teams     = Team::with('company', 'members.department')->orderBy('name')->get();
        $companies = Company::orderBy('name')->get();
        $members   = TeamMember::with('department')->orderBy('name')->get();
        return view('organization.teams', compact('teams', 'companies', 'members'));
    }

    public function store(Request $request)
    {
        $validated = $this->validatePayload($request);
        $memberIds = $request->input('member_ids', []);
        $team = Team::create($validated);
        if ($memberIds) {
            $team->members()->sync($this->normalizeMemberSync($memberIds));
        }
        return redirect()->route('org.teams-group.index')->with('success', 'Đã tạo đội nhóm.');
    }

    public function update(Request $request, Team $team)
    {
        $validated = $this->validatePayload($request);
        $team->update($validated);
        return redirect()->route('org.teams-group.index')->with('success', 'Đã cập nhật đội nhóm.');
    }

    public function destroy(Team $team)
    {
        $team->delete();
        return redirect()->route('org.teams-group.index')->with('success', 'Đã xóa đội nhóm.');
    }

    public function attachMember(Request $request, Team $team)
    {
        $data = $request->validate([
            'member_ids'    => 'required|array',
            'member_ids.*'  => 'exists:team_members,id',
            'role_in_team'  => 'nullable|string|max:50',
        ]);
        $sync = $this->normalizeMemberSync($data['member_ids'], $data['role_in_team'] ?? null);
        $team->members()->syncWithoutDetaching($sync);
        return redirect()->route('org.teams-group.index')->with('success', 'Đã thêm thành viên vào đội.');
    }

    public function detachMember(Team $team, TeamMember $teamMember)
    {
        $team->members()->detach($teamMember->id);
        return redirect()->route('org.teams-group.index')->with('success', 'Đã gỡ thành viên khỏi đội.');
    }

    private function validatePayload(Request $request): array
    {
        return $request->validate([
            'company_id'  => 'nullable|exists:companies,id',
            'name'        => 'required|string|max:150',
            'code'        => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
            'color'       => 'nullable|string|max:7',
        ]);
    }

    private function normalizeMemberSync(array $memberIds, ?string $roleInTeam = null): array
    {
        $sync = [];
        foreach ($memberIds as $id) {
            $sync[(int) $id] = ['role_in_team' => $roleInTeam];
        }
        return $sync;
    }
}
