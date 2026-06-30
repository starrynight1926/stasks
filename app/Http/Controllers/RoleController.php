<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Support\PermissionCatalog;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles   = Role::with('permissions')->withCount('members')->orderBy('name')->get();
        $modules = PermissionCatalog::modules();
        return view('organization.roles', compact('roles', 'modules'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:100|unique:roles,name',
            'description'      => 'nullable|string|max:300',
            'is_default'       => 'nullable|boolean',
            'permission_keys'  => 'nullable|array',
            'permission_keys.*' => 'string',
        ]);

        $role = Role::create([
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_default'  => (bool) ($validated['is_default'] ?? false),
        ]);

        $this->syncPermissionKeys($role, $validated['permission_keys'] ?? []);

        return redirect()->route('org.roles.index')->with('success', 'Đã tạo vai trò.');
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:100|unique:roles,name,' . $role->id,
            'description'       => 'nullable|string|max:300',
            'is_default'        => 'nullable|boolean',
            'permission_keys'   => 'nullable|array',
            'permission_keys.*' => 'string',
        ]);

        $role->update([
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_default'  => (bool) ($validated['is_default'] ?? false),
        ]);

        $this->syncPermissionKeys($role, $validated['permission_keys'] ?? []);

        return redirect()->route('org.roles.index')->with('success', 'Đã cập nhật vai trò.');
    }

    public function syncPermissions(Request $request, Role $role)
    {
        $data = $request->validate([
            'permission_keys'   => 'nullable|array',
            'permission_keys.*' => 'string',
        ]);
        $this->syncPermissionKeys($role, $data['permission_keys'] ?? []);
        return redirect()->route('org.roles.index')->with('success', 'Đã cập nhật quyền cho vai trò.');
    }

    public function destroy(Role $role)
    {
        if ($role->members()->count() > 0) {
            return redirect()->route('org.roles.index')->withErrors(['role' => 'Không thể xóa vai trò đang được nhân sự sử dụng.']);
        }
        $role->delete();
        return redirect()->route('org.roles.index')->with('success', 'Đã xóa vai trò.');
    }

    private function syncPermissionKeys(Role $role, array $keys): void
    {
        $ids = Permission::whereIn('key', $keys)->pluck('id')->all();
        $role->permissions()->sync($ids);
    }
}
