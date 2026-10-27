<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Department;
use App\Models\TeamMember;
use Illuminate\Http\Request;

class EnterpriseController extends Controller
{
    public function index()
    {
        if (session('is_admin')) {
            $companies = Company::with('branches')->orderBy('name')->get();
            $branchIds = $companies->pluck('branches.*.id')->flatten()->all();
            $managers = TeamMember::whereIn('branch_id', $branchIds)->where('level', 'manager')->get()->keyBy('branch_id');
            $candidates = TeamMember::orderBy('name')->get(['id', 'name', 'branch_id', 'level']);
            return view('enterprise.admin', compact('companies', 'candidates', 'managers'));
        }

        $me = $this->ensureOpsAdmin();
        if (!$me) abort(403, 'Chỉ Admin vận hành được truy cập trang này.');

        $company = Company::with('branches.departments')->find($me->branch->company_id);
        $departments = Department::whereIn('branch_id', $company->branches->pluck('id'))->orderBy('name')->get();
        $members = TeamMember::whereIn('branch_id', $company->branches->pluck('id'))->orderBy('name')->get();

        return view('enterprise.ops', compact('company', 'departments', 'members'));
    }

    public function storeCompany(Request $request)
    {
        abort_unless(session('is_admin'), 403);
        $data = $request->validate([
            'name' => 'required|string|max:200',
            'code' => 'nullable|string|max:50',
        ]);
        $company = Company::create($data);
        Branch::create([
            'name' => $data['name'] . ' - Trụ sở',
            'code' => ($data['code'] ?? 'CO') . '-HQ',
            'company_id' => $company->id,
        ]);
        return redirect()->route('enterprise.manage')->with('success', 'Đã tạo công ty + chi nhánh mặc định.');
    }

    public function assignOpsAdmin(Request $request, Company $company)
    {
        abort_unless(session('is_admin'), 403);
        $data = $request->validate([
            'member_id' => 'required|exists:team_members,id',
        ]);
        $branch = $company->branches()->first();
        abort_unless($branch, 400, 'Công ty chưa có chi nhánh.');

        TeamMember::where('branch_id', $branch->id)
            ->where('level', 'manager')
            ->update(['level' => 'member']);

        TeamMember::where('id', $data['member_id'])->update([
            'branch_id' => $branch->id,
            'level' => 'manager',
        ]);
        return redirect()->route('enterprise.manage')->with('success', 'Đã chỉ định Admin vận hành.');
    }

    public function storeDepartment(Request $request)
    {
        $me = $this->ensureOpsAdmin();
        abort_unless($me, 403);
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'branch_id' => 'required|exists:branches,id',
        ]);
        $branch = Branch::find($data['branch_id']);
        abort_unless($branch && $branch->company_id === $me->branch->company_id, 403);
        Department::create($data);
        return redirect()->route('enterprise.manage')->with('success', 'Đã tạo phòng ban.');
    }

    public function assignMember(Request $request)
    {
        $me = $this->ensureOpsAdmin();
        abort_unless($me, 403);
        $data = $request->validate([
            'member_id' => 'required|exists:team_members,id',
            'department_id' => 'nullable|exists:departments,id',
            'level' => 'nullable|in:manager,lead,member',
        ]);
        $m = TeamMember::find($data['member_id']);
        $companyBranchIds = Branch::where('company_id', $me->branch->company_id)->pluck('id')->all();
        abort_unless(in_array($m->branch_id, $companyBranchIds, true), 403, 'Member không thuộc công ty của bạn.');

        if ($data['department_id'] ?? null) {
            $dept = Department::find($data['department_id']);
            abort_unless(in_array($dept->branch_id, $companyBranchIds, true), 403);
        }
        $m->update([
            'department_id' => $data['department_id'] ?? null,
            'level' => $data['level'] ?? $m->level,
        ]);
        return redirect()->route('enterprise.manage')->with('success', 'Đã cập nhật nhân sự.');
    }

    private function ensureOpsAdmin(): ?TeamMember
    {
        $id = session('member_id');
        if (!$id) return null;
        $m = TeamMember::with('branch')->find($id);
        if (!$m || $m->level !== 'manager' || !$m->branch || !$m->branch->company_id) return null;
        return $m;
    }
}
