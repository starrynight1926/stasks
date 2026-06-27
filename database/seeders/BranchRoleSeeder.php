<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Permission;
use App\Models\Role;
use App\Models\TeamMember;
use App\Models\Department;
use App\Models\Task;
use App\Support\PermissionCatalog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class BranchRoleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Default branch
        $branch = Branch::firstOrCreate(
            ['name' => 'Cơ sở chính'],
            ['code' => 'main', 'address' => null, 'phone' => null]
        );

        // 2. Permissions catalog
        foreach (PermissionCatalog::flat() as $row) {
            Permission::firstOrCreate(
                ['key' => $row['key']],
                ['module' => $row['module'], 'label' => $row['label']]
            );
        }

        // 3. Roles
        $allPermIds = Permission::pluck('id')->all();

        $adminRole = Role::firstOrCreate(
            ['name' => 'Admin'],
            ['description' => 'Toàn quyền trong cơ sở.', 'is_default' => false]
        );
        $adminRole->permissions()->sync($allPermIds);

        $managerKeys = [
            'task.view','task.create','task.edit','task.delete','task.change_status',
            'subtask.create','subtask.edit','subtask.delete','subtask.toggle','subtask.cancel',
            'comment.view','comment.create','comment.delete',
            'file.view','file.upload','file.download','file.delete',
            'member.view','department.view',
        ];
        $managerRole = Role::firstOrCreate(
            ['name' => 'Manager'],
            ['description' => 'Quản lý task và xem nhân sự / phòng ban.', 'is_default' => false]
        );
        $managerRole->permissions()->sync(Permission::whereIn('key', $managerKeys)->pluck('id'));

        $staffKeys = [
            'task.view','task.create','task.edit','task.change_status',
            'subtask.create','subtask.edit','subtask.toggle','subtask.cancel',
            'comment.view','comment.create',
            'file.view','file.upload','file.download',
            'member.view','department.view',
        ];
        $staffRole = Role::firstOrCreate(
            ['name' => 'Nhân viên'],
            ['description' => 'Quyền cơ bản: tạo task, comment, upload file.', 'is_default' => true]
        );
        $staffRole->permissions()->sync(Permission::whereIn('key', $staffKeys)->pluck('id'));

        $viewerKeys = ['task.view','comment.view','file.view','file.download','member.view','department.view'];
        $viewerRole = Role::firstOrCreate(
            ['name' => 'Khách (chỉ xem)'],
            ['description' => 'Chỉ xem, không thao tác.', 'is_default' => false]
        );
        $viewerRole->permissions()->sync(Permission::whereIn('key', $viewerKeys)->pluck('id'));

        // 4. Backfill existing data → default branch
        Department::whereNull('branch_id')->update(['branch_id' => $branch->id]);
        TeamMember::whereNull('branch_id')->update(['branch_id' => $branch->id]);
        Task::whereNull('branch_id')->update(['branch_id' => $branch->id]);

        // 5. Seed nv1, nv2, nv3 members (or update if exist)
        $seedMembers = [
            ['username' => 'nv1', 'name' => 'Nhân viên 1', 'role_id' => $staffRole->id],
            ['username' => 'nv2', 'name' => 'Nhân viên 2', 'role_id' => $staffRole->id],
            ['username' => 'nv3', 'name' => 'Nhân viên 3', 'role_id' => $staffRole->id],
        ];

        foreach ($seedMembers as $m) {
            $member = TeamMember::where('username', $m['username'])->first();
            if (!$member) {
                TeamMember::create([
                    'branch_id' => $branch->id,
                    'role_id' => $m['role_id'],
                    'username' => $m['username'],
                    'password' => Hash::make($m['username']),
                    'name' => $m['name'],
                    'email' => $m['username'] . '@local.test',
                    'role' => 'Nhân viên',
                    'must_change_password' => false,
                ]);
            } else {
                $member->update([
                    'branch_id' => $member->branch_id ?? $branch->id,
                    'role_id' => $member->role_id ?? $m['role_id'],
                    'password' => Hash::make($m['username']),
                ]);
            }
        }
    }
}
