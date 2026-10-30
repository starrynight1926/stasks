<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Department;
use App\Models\Role;
use App\Models\Task;
use App\Models\TeamMember;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoCompanySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Company + branch riêng cho demo
        $company = Company::firstOrCreate(
            ['code' => 'ABC'],
            ['name' => 'Công ty TNHH ABC', 'description' => 'Công ty demo cho tracking task theo phòng ban.']
        );

        $branch = Branch::firstOrCreate(
            ['code' => 'abc-main'],
            ['name' => 'ABC - Trụ sở chính', 'company_id' => $company->id]
        );

        // 2. Lấy role từ BranchRoleSeeder
        $managerRole = Role::where('name', 'Manager')->first();
        $staffRole   = Role::where('name', 'Nhân viên')->first();

        if (!$managerRole || !$staffRole) {
            $this->command?->warn('Chạy BranchRoleSeeder trước để có role Manager/Nhân viên.');
            return;
        }

        // 3. 4 phòng ban
        $deptsConfig = [
            ['code' => 'ABC_KT',  'name' => 'Phòng Kế toán', 'color' => '#F59E0B'],
            ['code' => 'ABC_MKT', 'name' => 'Phòng Marketing', 'color' => '#10B981'],
            ['code' => 'ABC_HR',  'name' => 'Phòng Nhân sự', 'color' => '#8B5CF6'],
            ['code' => 'ABC_IT',  'name' => 'Phòng IT', 'color' => '#3B82F6'],
        ];

        $depts = [];
        foreach ($deptsConfig as $d) {
            $depts[$d['code']] = Department::firstOrCreate(
                ['code' => $d['code']],
                ['name' => $d['name'], 'color' => $d['color'], 'branch_id' => $branch->id]
            );
        }

        // 4. Members: mỗi phòng 1 lead + 2 nhân viên
        $plan = [
            'ABC_KT'  => [['abc_kt_lead', 'Trần Kế Toán Trưởng', 'lead'], ['abc_kt_nv1', 'Nguyễn Thị Sổ Sách', 'member'], ['abc_kt_nv2', 'Phạm Văn Hóa Đơn', 'member']],
            'ABC_MKT' => [['abc_mkt_lead', 'Lê Marketing Trưởng', 'lead'], ['abc_mkt_nv1', 'Hoàng Thị Content', 'member'], ['abc_mkt_nv2', 'Vũ Văn Quảng Cáo', 'member']],
            'ABC_HR'  => [['abc_hr_lead', 'Đỗ Nhân Sự Trưởng', 'lead'], ['abc_hr_nv1', 'Bùi Thị Tuyển Dụng', 'member'], ['abc_hr_nv2', 'Ngô Văn Lương', 'member']],
            'ABC_IT'  => [['abc_it_lead', 'Mai IT Trưởng', 'lead'], ['abc_it_nv1', 'Lý Thị Dev', 'member'], ['abc_it_nv2', 'Trịnh Văn Server', 'member']],
        ];

        $members = []; // $members['abc_kt_lead'] => TeamMember
        foreach ($plan as $deptCode => $rows) {
            $dept = $depts[$deptCode];
            $leadRow = $rows[0];
            // Tạo lead trước để lấy id làm parent cho member
            $lead = TeamMember::firstOrCreate(
                ['username' => $leadRow[0]],
                [
                    'branch_id' => $branch->id,
                    'department_id' => $dept->id,
                    'role_id' => $managerRole->id,
                    'password' => Hash::make($leadRow[0]),
                    'name' => $leadRow[1],
                    'email' => $leadRow[0] . '@abc.test',
                    'role' => 'Trưởng phòng',
                    'position' => 'Trưởng phòng',
                    'level' => 'lead',
                    'must_change_password' => false,
                ]
            );
            $members[$leadRow[0]] = $lead;

            foreach (array_slice($rows, 1) as $m) {
                $staff = TeamMember::firstOrCreate(
                    ['username' => $m[0]],
                    [
                        'branch_id' => $branch->id,
                        'department_id' => $dept->id,
                        'role_id' => $staffRole->id,
                        'password' => Hash::make($m[0]),
                        'name' => $m[1],
                        'email' => $m[0] . '@abc.test',
                        'role' => 'Nhân viên',
                        'position' => 'Nhân viên',
                        'level' => 'member',
                        'parent_id' => $lead->id,
                        'must_change_password' => false,
                    ]
                );
                $members[$m[0]] = $staff;
            }
        }

        // 5. Demo task cho từng phòng
        $today = now()->toDateString();
        $soon  = now()->addDays(7)->toDateString();
        $later = now()->addDays(14)->toDateString();
        $past  = now()->subDays(3)->toDateString();

        $tasks = [
            // Kế toán
            ['Phòng Kế toán', 'ABC_KT', 'abc_kt_lead', 'Lập báo cáo tài chính tháng', 'in_progress', 'high', $past, $soon, 40],
            ['Phòng Kế toán', 'ABC_KT', 'abc_kt_nv1', 'Đối chiếu công nợ khách hàng', 'todo', 'medium', $today, $later, 0],
            ['Phòng Kế toán', 'ABC_KT', 'abc_kt_nv2', 'Nhập hóa đơn đầu vào tuần này', 'in_progress', 'medium', $today, $soon, 60],
            ['Phòng Kế toán', 'ABC_KT', 'abc_kt_nv2', 'Nộp thuế GTGT quý trước', 'done', 'high', $past, $past, 100],

            // Marketing
            ['Phòng Marketing', 'ABC_MKT', 'abc_mkt_lead', 'Lên kế hoạch campaign Black Friday', 'in_progress', 'high', $today, $later, 30],
            ['Phòng Marketing', 'ABC_MKT', 'abc_mkt_nv1', 'Viết 10 bài blog SEO sản phẩm', 'in_progress', 'medium', $past, $soon, 50],
            ['Phòng Marketing', 'ABC_MKT', 'abc_mkt_nv2', 'Chạy quảng cáo Facebook tuần này', 'todo', 'high', $today, $soon, 0],

            // Nhân sự
            ['Phòng Nhân sự', 'ABC_HR', 'abc_hr_lead', 'Tuyển 2 vị trí Dev Backend', 'in_progress', 'urgent', $past, $later, 25],
            ['Phòng Nhân sự', 'ABC_HR', 'abc_hr_nv1', 'Lọc CV ứng viên BA', 'in_progress', 'medium', $today, $soon, 70],
            ['Phòng Nhân sự', 'ABC_HR', 'abc_hr_nv2', 'Chuẩn bị bảng lương tháng', 'todo', 'high', $today, $soon, 0],

            // IT
            ['Phòng IT', 'ABC_IT', 'abc_it_lead', 'Review kiến trúc hệ thống mới', 'in_progress', 'high', $past, $later, 50],
            ['Phòng IT', 'ABC_IT', 'abc_it_nv1', 'Sửa bug trang checkout', 'review', 'urgent', $past, $today, 90],
            ['Phòng IT', 'ABC_IT', 'abc_it_nv2', 'Backup và kiểm tra server tuần này', 'todo', 'medium', $today, $soon, 0],
            ['Phòng IT', 'ABC_IT', 'abc_it_nv1', 'Cài CI/CD cho project nội bộ', 'done', 'medium', $past, $past, 100],
        ];

        foreach ($tasks as $row) {
            [$deptName, $deptCode, $assigneeUsername, $title, $status, $priority, $start, $due, $progress] = $row;
            $dept = $depts[$deptCode];
            $assignee = $members[$assigneeUsername];

            Task::firstOrCreate(
                ['title' => $title, 'department_id' => $dept->id],
                [
                    'description' => 'Demo task cho ' . $deptName,
                    'assignee_id' => $assignee->id,
                    'created_by'  => $assignee->id,
                    'branch_id'   => $branch->id,
                    'company_id'  => $company->id,
                    'status'      => $status,
                    'priority'    => $priority,
                    'start_date'  => $start,
                    'due_date'    => $due,
                    'progress'    => $progress,
                    'scope'       => 'department',
                    'visibility'  => 'department',
                    'done_at'     => $status === 'done' ? now() : null,
                ]
            );
        }

        $this->command?->info('Seeded công ty ABC: 4 phòng, ' . count($members) . ' members, ' . count($tasks) . ' tasks.');
    }
}
