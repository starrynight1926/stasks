<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Task;
use App\Models\TeamMember;
use Database\Seeders\BranchRoleSeeder;
use Database\Seeders\DemoCompanySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepartmentTrackingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(BranchRoleSeeder::class);
        $this->seed(DemoCompanySeeder::class);
    }

    private function login(string $username): TeamMember
    {
        $m = TeamMember::where('username', $username)->firstOrFail();
        $this->post('/login', ['username' => $username, 'password' => $username])
             ->assertRedirect(route('quick-tasks'));
        return $m;
    }

    public function test_lead_can_login_with_seeded_password(): void
    {
        $this->login('abc_kt_lead');
        $this->assertTrue(session('authenticated'));
        $this->assertSame('lead', session('level'));
        $this->assertSame('Manager', session('role_name'));
    }

    public function test_wrong_password_rejected(): void
    {
        $this->post('/login', ['username' => 'abc_kt_lead', 'password' => 'wrong'])
             ->assertSessionHasErrors('login');
        $this->assertNull(session('authenticated'));
    }

    public function test_member_level_is_set_for_staff(): void
    {
        $this->login('abc_kt_nv1');
        $this->assertSame('member', session('level'));
        $this->assertSame('Nhân viên', session('role_name'));
    }

    public function test_board_shows_task_from_own_department(): void
    {
        $lead = $this->login('abc_kt_lead');
        $kt   = Department::where('code', 'ABC_KT')->first();
        $task = Task::where('department_id', $kt->id)->first();

        $res = $this->get(route('tasks.board'));
        $res->assertStatus(200);
        $res->assertSee($task->title);
    }

    public function test_board_hides_tasks_from_other_department(): void
    {
        $this->login('abc_kt_nv1'); // KT staff
        $mkt = Department::where('code', 'ABC_MKT')->first();
        $mktTask = Task::where('department_id', $mkt->id)
            ->where('status', 'todo')
            ->first();
        $this->assertNotNull($mktTask);

        $res = $this->get(route('tasks.list'));
        $res->assertStatus(200);
        $res->assertDontSee($mktTask->title);
    }

    public function test_update_status_to_done_sets_progress_100(): void
    {
        $lead = $this->login('abc_kt_lead');
        $task = Task::whereNull('parent_id')
            ->where('assignee_id', $lead->id)
            ->where('status', '!=', 'done')
            ->first();
        $this->assertNotNull($task);

        $this->patch(route('tasks.updateStatus', $task), ['status' => 'done'])
             ->assertStatus(302); // redirect back

        $task->refresh();
        $this->assertSame('done', $task->status);
        $this->assertEqualsWithDelta(100, (float) $task->progress, 0.01);
        $this->assertNotNull($task->done_at);
    }

    public function test_lead_can_quick_reassign_task_to_subordinate(): void
    {
        $lead = $this->login('abc_kt_lead');
        $nv2  = TeamMember::where('username', 'abc_kt_nv2')->first();
        $task = Task::where('assignee_id', $lead->id)->first();
        $this->assertNotNull($task);

        $res = $this->patchJson(route('tasks.quickUpdate', $task), [
            'assignee_id' => $nv2->id,
        ]);
        $res->assertOk();
        $res->assertJsonPath('assignee.id', $nv2->id);

        $task->refresh();
        $this->assertSame($nv2->id, $task->assignee_id);
    }

    public function test_subtask_done_updates_parent_progress(): void
    {
        $lead = $this->login('abc_it_lead');
        $kt   = Department::where('code', 'ABC_IT')->first();

        // Tạo parent + 2 subtasks (equal weight)
        $parent = Task::create([
            'title'        => 'Parent demo tracking',
            'department_id'=> $kt->id,
            'assignee_id'  => $lead->id,
            'created_by'   => $lead->name,
            'status'       => 'in_progress',
            'priority'     => 'medium',
            'progress'     => 0,
            'scope'        => 'department',
        ]);
        $s1 = Task::create([
            'title' => 'Sub A', 'parent_id' => $parent->id,
            'assignee_id' => $lead->id, 'status' => 'todo',
            'priority' => 'medium', 'progress' => 0,
        ]);
        $s2 = Task::create([
            'title' => 'Sub B', 'parent_id' => $parent->id,
            'assignee_id' => $lead->id, 'status' => 'todo',
            'priority' => 'medium', 'progress' => 0,
        ]);

        // Hoàn thành 1 subtask → parent progress = 50
        $res = $this->patchJson(route('tasks.updateStatus', $s1), ['status' => 'done']);
        $res->assertOk();
        $this->assertEqualsWithDelta(50, $res->json('parent_progress'), 0.01);

        $parent->refresh();
        $this->assertEqualsWithDelta(50, (float) $parent->progress, 0.01);

        // Done nốt → 100
        $r2 = $this->patchJson(route('tasks.updateStatus', $s2), ['status' => 'done']);
        $this->assertEqualsWithDelta(100, $r2->json('parent_progress'), 0.01);
        $parent->refresh();
        $this->assertEqualsWithDelta(100, (float) $parent->progress, 0.01);
    }

    public function test_subtask_weight_based_progress(): void
    {
        $lead = $this->login('abc_it_lead');
        $parent = Task::create([
            'title' => 'Weighted parent', 'assignee_id' => $lead->id,
            'created_by' => $lead->name, 'status' => 'in_progress',
            'priority' => 'medium', 'progress' => 0,
        ]);
        $heavy = Task::create([
            'title' => 'Heavy', 'parent_id' => $parent->id, 'assignee_id' => $lead->id,
            'status' => 'todo', 'priority' => 'medium', 'weight' => 70, 'progress' => 0,
        ]);
        Task::create([
            'title' => 'Light', 'parent_id' => $parent->id, 'assignee_id' => $lead->id,
            'status' => 'todo', 'priority' => 'medium', 'weight' => 30, 'progress' => 0,
        ]);

        $r = $this->patchJson(route('tasks.updateStatus', $heavy), ['status' => 'done']);
        $this->assertEqualsWithDelta(70, $r->json('parent_progress'), 0.01);
    }

    public function test_member_level_cannot_access_create_task(): void
    {
        $this->login('abc_mkt_nv1');
        $res = $this->get(route('tasks.create'));
        // middleware NotMemberLevel → redirect back với flash error
        $res->assertRedirect();
        $this->assertSame('Bạn không có quyền thao tác chức năng này.', session('error'));
    }

    public function test_lead_can_access_create_task(): void
    {
        $this->login('abc_mkt_lead');
        $this->get(route('tasks.create'))->assertStatus(200);
    }

    public function test_summary_endpoint_returns_progress_payload(): void
    {
        $lead = $this->login('abc_kt_lead');
        $task = Task::where('assignee_id', $lead->id)->first();

        $this->getJson(route('tasks.summary', $task))
            ->assertOk()
            ->assertJsonStructure(['id', 'title', 'status', 'progress', 'assignee', 'department']);
    }

    public function test_cancel_subtask_excluded_from_progress(): void
    {
        $lead = $this->login('abc_it_lead');
        $parent = Task::create([
            'title' => 'Parent cancel case', 'assignee_id' => $lead->id,
            'created_by' => $lead->name, 'status' => 'in_progress',
            'priority' => 'medium', 'progress' => 0,
        ]);
        $a = Task::create(['title'=>'A','parent_id'=>$parent->id,'assignee_id'=>$lead->id,'status'=>'done','priority'=>'medium','progress'=>100]);
        $b = Task::create(['title'=>'B','parent_id'=>$parent->id,'assignee_id'=>$lead->id,'status'=>'todo','priority'=>'medium','progress'=>0]);
        $c = Task::create(['title'=>'C','parent_id'=>$parent->id,'assignee_id'=>$lead->id,'status'=>'todo','priority'=>'medium','progress'=>0]);

        // Cancel c với lý do → chỉ còn A(done) + B(todo) = 50%
        $r = $this->patchJson(route('tasks.subtasks.cancel', $c), ['reason' => 'Không cần nữa']);
        $r->assertOk();
        $this->assertEqualsWithDelta(50, $r->json('parent_progress'), 0.01);
    }
}
