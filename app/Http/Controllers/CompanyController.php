<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::withCount('branches', 'teams')->orderBy('name')->get();
        return view('organization.companies', compact('companies'));
    }

    public function store(Request $request)
    {
        $validated = $this->validatePayload($request);
        Company::create($validated);
        return redirect()->route('org.companies.index')->with('success', 'Đã tạo công ty.');
    }

    public function update(Request $request, Company $company)
    {
        $validated = $this->validatePayload($request, $company->id);
        $company->update($validated);
        return redirect()->route('org.companies.index')->with('success', 'Đã cập nhật công ty.');
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return redirect()->route('org.companies.index')->with('success', 'Đã xóa công ty.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:companies,id']);
        Company::whereIn('id', $request->ids)->delete();
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => count($request->ids) . ' công ty đã xóa.']);
        }
        return redirect()->route('org.companies.index')->with('success', count($request->ids) . ' công ty đã xóa.');
    }

    private function validatePayload(Request $request, ?int $ignoreId = null): array
    {
        $unique = 'unique:companies,code' . ($ignoreId ? ',' . $ignoreId : '');
        return $request->validate([
            'name'        => 'required|string|max:150',
            'code'        => "nullable|string|max:50|$unique",
            'tax_code'    => 'nullable|string|max:50',
            'address'     => 'nullable|string|max:500',
            'phone'       => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
        ]);
    }
}
