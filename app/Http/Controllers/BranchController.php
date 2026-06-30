<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Company;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $branches  = Branch::with('company')->withCount('departments', 'members')->orderBy('name')->get();
        $companies = Company::orderBy('name')->get();
        return view('organization.branches', compact('branches', 'companies'));
    }

    public function store(Request $request)
    {
        $validated = $this->validatePayload($request);
        Branch::create($validated);
        return redirect()->route('org.branches.index')->with('success', 'Đã tạo cơ sở.');
    }

    public function update(Request $request, Branch $branch)
    {
        $validated = $this->validatePayload($request, $branch->id);
        $branch->update($validated);
        return redirect()->route('org.branches.index')->with('success', 'Đã cập nhật cơ sở.');
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();
        return redirect()->route('org.branches.index')->with('success', 'Đã xóa cơ sở.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:branches,id']);
        Branch::whereIn('id', $request->ids)->delete();
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => count($request->ids) . ' cơ sở đã xóa.']);
        }
        return redirect()->route('org.branches.index')->with('success', count($request->ids) . ' cơ sở đã xóa.');
    }

    private function validatePayload(Request $request, ?int $ignoreId = null): array
    {
        $unique = 'unique:branches,code' . ($ignoreId ? ',' . $ignoreId : '');
        return $request->validate([
            'company_id' => 'nullable|exists:companies,id',
            'name'       => 'required|string|max:150',
            'code'       => "nullable|string|max:50|$unique",
            'address'    => 'nullable|string|max:500',
            'phone'      => 'nullable|string|max:50',
        ]);
    }
}
