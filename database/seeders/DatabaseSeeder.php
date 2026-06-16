<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Project;
use App\Models\TeamMember;
use App\Models\Task;
use App\Models\Tag;
use App\Models\Comment;
use App\Models\File;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $engineering = Department::create(['name' => 'Engineering', 'code' => 'ENG', 'color' => '#3B82F6', 'description' => 'Core development team responsible for building and maintaining the product infrastructure.', 'head_name' => 'Marcus Chen', 'member_count' => 8, 'active_projects' => 3, 'performance_score' => 87.5]);
        $design = Department::create(['name' => 'Design', 'code' => 'DSN', 'color' => '#8B5CF6', 'description' => 'UI/UX design team creating intuitive user experiences and visual systems.', 'head_name' => 'Sarah Kim', 'member_count' => 5, 'active_projects' => 2, 'performance_score' => 92.0]);
        $marketing = Department::create(['name' => 'Marketing', 'code' => 'MKT', 'color' => '#10B981', 'description' => 'Growth and marketing team driving user acquisition and brand awareness.', 'head_name' => 'Alex Rivera', 'member_count' => 4, 'active_projects' => 2, 'performance_score' => 78.3]);
        $qa = Department::create(['name' => 'Quality Assurance', 'code' => 'QA', 'color' => '#F59E0B', 'description' => 'Testing and quality assurance team ensuring product reliability.', 'head_name' => 'Linh Nguyen', 'member_count' => 3, 'active_projects' => 2, 'performance_score' => 95.0]);

        $project = Project::create(['name' => 'ProjectFlow Platform', 'description' => 'Build the next-gen project management platform', 'department_id' => $engineering->id, 'status' => 'active', 'start_date' => '2024-09-01', 'end_date' => '2025-03-31', 'progress' => 64]);

        $marcus = TeamMember::create(['name' => 'Marcus Chen', 'email' => 'marcus@projectflow.io', 'role' => 'Tech Lead', 'position' => 'Senior Engineer', 'department_id' => $engineering->id, 'active_tasks' => 5, 'workload_percent' => 85]);
        $sarah = TeamMember::create(['name' => 'Sarah Kim', 'email' => 'sarah@projectflow.io', 'role' => 'Design Lead', 'position' => 'UI/UX Designer', 'department_id' => $design->id, 'active_tasks' => 3, 'workload_percent' => 60]);
        $alex = TeamMember::create(['name' => 'Alex Rivera', 'email' => 'alex@projectflow.io', 'role' => 'Product Manager', 'position' => 'Senior PM', 'department_id' => $marketing->id, 'active_tasks' => 4, 'workload_percent' => 70]);
        $linh = TeamMember::create(['name' => 'Linh Nguyen', 'email' => 'linh@projectflow.io', 'role' => 'QA Engineer', 'position' => 'Senior QA', 'department_id' => $qa->id, 'active_tasks' => 6, 'workload_percent' => 90]);
        $david = TeamMember::create(['name' => 'David Park', 'email' => 'david@projectflow.io', 'role' => 'Backend Dev', 'position' => 'Mid Engineer', 'department_id' => $engineering->id, 'active_tasks' => 4, 'workload_percent' => 65]);
        $emma = TeamMember::create(['name' => 'Emma Wilson', 'email' => 'emma@projectflow.io', 'role' => 'Frontend Dev', 'position' => 'Junior Engineer', 'department_id' => $engineering->id, 'active_tasks' => 3, 'workload_percent' => 55]);
        $minh = TeamMember::create(['name' => 'Minh Tran', 'email' => 'minh@projectflow.io', 'role' => 'DevOps', 'position' => 'Infrastructure', 'department_id' => $engineering->id, 'active_tasks' => 2, 'workload_percent' => 40]);
        $yuki = TeamMember::create(['name' => 'Yuki Tanaka', 'email' => 'yuki@projectflow.io', 'role' => 'UI Designer', 'position' => 'Designer', 'department_id' => $design->id, 'active_tasks' => 3, 'workload_percent' => 50]);

        $frontend = Tag::create(['name' => 'Frontend', 'color' => '#3B82F6']);
        $backend = Tag::create(['name' => 'Backend', 'color' => '#10B981']);
        $critical = Tag::create(['name' => 'Critical', 'color' => '#EF4444']);
        $feature = Tag::create(['name' => 'Feature', 'color' => '#8B5CF6']);
        $bugfix = Tag::create(['name' => 'Bug Fix', 'color' => '#F59E0B']);
        $infra = Tag::create(['name' => 'Infrastructure', 'color' => '#64748B']);

        $t1 = Task::create(['title' => 'Implement Real-time Canvas Rendering Engine', 'description' => 'Build the core WebGL rendering pipeline for interactive project timeline canvas with smooth 60fps animations.', 'project_id' => $project->id, 'assignee_id' => $marcus->id, 'department_id' => $engineering->id, 'status' => 'in_progress', 'priority' => 'high', 'start_date' => '2024-10-01', 'due_date' => '2024-11-15', 'progress' => 65, 'sort_order' => 1]);
        $t2 = Task::create(['title' => 'Design System Component Library v2', 'description' => 'Rebuild component library with new design tokens, accessibility improvements, and dark mode support.', 'project_id' => $project->id, 'assignee_id' => $sarah->id, 'department_id' => $design->id, 'status' => 'in_progress', 'priority' => 'high', 'start_date' => '2024-10-05', 'due_date' => '2024-11-20', 'progress' => 45, 'sort_order' => 2]);
        $t3 = Task::create(['title' => 'Implement AutoFill for Single Sign-On', 'description' => 'Add SSO integration with Google, GitHub, and Microsoft providers.', 'project_id' => $project->id, 'assignee_id' => $david->id, 'department_id' => $engineering->id, 'status' => 'review', 'priority' => 'urgent', 'start_date' => '2024-09-15', 'due_date' => '2024-10-20', 'progress' => 90, 'sort_order' => 1]);
        $t4 = Task::create(['title' => 'API Rate Limiting & Throttling', 'description' => 'Implement rate limiting middleware with Redis-backed token bucket algorithm.', 'project_id' => $project->id, 'assignee_id' => $minh->id, 'department_id' => $engineering->id, 'status' => 'done', 'priority' => 'medium', 'start_date' => '2024-09-01', 'due_date' => '2024-09-30', 'progress' => 100, 'sort_order' => 1]);
        $t5 = Task::create(['title' => 'Drag & Drop Kanban Board', 'description' => 'Interactive drag-and-drop functionality for the Kanban board with real-time status updates.', 'project_id' => $project->id, 'assignee_id' => $emma->id, 'department_id' => $engineering->id, 'status' => 'in_progress', 'priority' => 'high', 'start_date' => '2024-10-10', 'due_date' => '2024-11-10', 'progress' => 30, 'sort_order' => 3]);
        $t6 = Task::create(['title' => 'User Dashboard Analytics', 'description' => 'Build dashboard with KPIs, charts, and performance metrics for project overview.', 'project_id' => $project->id, 'assignee_id' => $emma->id, 'department_id' => $engineering->id, 'status' => 'todo', 'priority' => 'medium', 'start_date' => '2024-11-01', 'due_date' => '2024-11-30', 'progress' => 0, 'sort_order' => 1]);
        $t7 = Task::create(['title' => 'Mobile Responsive Layout', 'description' => 'Ensure all views work on tablets and mobile devices.', 'project_id' => $project->id, 'assignee_id' => $yuki->id, 'department_id' => $design->id, 'status' => 'todo', 'priority' => 'medium', 'start_date' => '2024-11-15', 'due_date' => '2024-12-15', 'progress' => 0, 'sort_order' => 2]);
        $t8 = Task::create(['title' => 'Gantt Chart Timeline View', 'description' => 'Build interactive Gantt chart with dependency arrows, drag-to-resize, and zoom levels.', 'project_id' => $project->id, 'assignee_id' => $marcus->id, 'department_id' => $engineering->id, 'status' => 'todo', 'priority' => 'high', 'start_date' => '2024-12-01', 'due_date' => '2025-01-15', 'progress' => 0, 'sort_order' => 3]);
        $t9 = Task::create(['title' => 'File Management System', 'description' => 'Central file storage with upload, preview, search, and version history.', 'project_id' => $project->id, 'assignee_id' => $david->id, 'department_id' => $engineering->id, 'status' => 'in_progress', 'priority' => 'medium', 'start_date' => '2024-10-15', 'due_date' => '2024-11-25', 'progress' => 20, 'sort_order' => 4]);
        $t10 = Task::create(['title' => 'End-to-End Testing Suite', 'description' => 'Comprehensive E2E test coverage for all critical user flows.', 'project_id' => $project->id, 'assignee_id' => $linh->id, 'department_id' => $qa->id, 'status' => 'review', 'priority' => 'medium', 'start_date' => '2024-10-01', 'due_date' => '2024-10-31', 'progress' => 80, 'sort_order' => 2]);
        $t11 = Task::create(['title' => 'Database Migration & Optimization', 'description' => 'Optimize database queries, add indexes, and migrate to new schema.', 'project_id' => $project->id, 'assignee_id' => $minh->id, 'department_id' => $engineering->id, 'status' => 'done', 'priority' => 'urgent', 'start_date' => '2024-09-01', 'due_date' => '2024-09-15', 'progress' => 100, 'sort_order' => 2]);
        $t12 = Task::create(['title' => 'Notification System', 'description' => 'Real-time notifications with email digest and in-app bell notifications.', 'project_id' => $project->id, 'assignee_id' => $david->id, 'department_id' => $engineering->id, 'status' => 'todo', 'priority' => 'low', 'start_date' => '2024-12-01', 'due_date' => '2025-01-31', 'progress' => 0, 'sort_order' => 4]);

        Task::create(['title' => 'Architecture Planning', 'parent_id' => $t1->id, 'project_id' => $project->id, 'assignee_id' => $marcus->id, 'status' => 'done', 'priority' => 'high', 'weight' => 20, 'progress' => 100]);
        Task::create(['title' => 'WebGL Pipeline Setup', 'parent_id' => $t1->id, 'project_id' => $project->id, 'assignee_id' => $marcus->id, 'status' => 'done', 'priority' => 'high', 'weight' => 30, 'progress' => 100]);
        Task::create(['title' => 'Animation & Interaction Layer', 'parent_id' => $t1->id, 'project_id' => $project->id, 'assignee_id' => $emma->id, 'status' => 'in_progress', 'priority' => 'high', 'weight' => 30, 'progress' => 40]);
        Task::create(['title' => 'Performance Optimization', 'parent_id' => $t1->id, 'project_id' => $project->id, 'assignee_id' => $marcus->id, 'status' => 'todo', 'priority' => 'medium', 'weight' => 20, 'progress' => 0]);

        $t1->tags()->attach([$frontend->id, $critical->id]);
        $t2->tags()->attach([$frontend->id, $feature->id]);
        $t3->tags()->attach([$backend->id, $critical->id]);
        $t4->tags()->attach([$backend->id, $infra->id]);
        $t5->tags()->attach([$frontend->id, $feature->id]);
        $t6->tags()->attach([$frontend->id]);
        $t8->tags()->attach([$frontend->id, $feature->id]);
        $t9->tags()->attach([$backend->id, $feature->id]);
        $t10->tags()->attach([$bugfix->id]);
        $t11->tags()->attach([$backend->id, $infra->id]);

        $t5->dependencies()->attach($t2->id);
        $t8->dependencies()->attach($t1->id);

        Comment::create(['task_id' => $t1->id, 'team_member_id' => $marcus->id, 'body' => 'WebGL pipeline is set up. Moving to animation layer next. Frame rate is hitting 60fps consistently.']);
        Comment::create(['task_id' => $t1->id, 'team_member_id' => $emma->id, 'body' => 'Started working on the interaction layer. The gesture handling is complex but making progress.']);
        Comment::create(['task_id' => $t3->id, 'team_member_id' => $david->id, 'body' => 'SSO integration complete for Google and GitHub. Microsoft provider needs additional testing.']);
        Comment::create(['task_id' => $t3->id, 'team_member_id' => $linh->id, 'body' => 'Tested the Google SSO flow - works perfectly. Will test Microsoft after David finishes.']);
        Comment::create(['task_id' => $t5->id, 'team_member_id' => $emma->id, 'body' => 'Drag and drop basic functionality working. Need to add animation transitions.']);

        File::create(['name' => 'architecture-v2.pdf', 'original_name' => 'Architecture Design v2.pdf', 'path' => 'files/architecture-v2.pdf', 'mime_type' => 'application/pdf', 'size' => 2450000, 'type' => 'document', 'project_id' => $project->id, 'uploaded_by' => $marcus->id]);
        File::create(['name' => 'wireframes-dashboard.fig', 'original_name' => 'Dashboard Wireframes.fig', 'path' => 'files/wireframes-dashboard.fig', 'mime_type' => 'application/octet-stream', 'size' => 8200000, 'type' => 'document', 'project_id' => $project->id, 'uploaded_by' => $sarah->id]);
        File::create(['name' => 'api-spec-v3.yaml', 'original_name' => 'API Specification v3.yaml', 'path' => 'files/api-spec-v3.yaml', 'mime_type' => 'text/yaml', 'size' => 156000, 'type' => 'document', 'project_id' => $project->id, 'uploaded_by' => $david->id]);
        File::create(['name' => 'screenshot-kanban.png', 'original_name' => 'Kanban Board Screenshot.png', 'path' => 'files/screenshot-kanban.png', 'mime_type' => 'image/png', 'size' => 1800000, 'type' => 'image', 'project_id' => $project->id, 'task_id' => $t5->id, 'uploaded_by' => $emma->id]);
        File::create(['name' => 'test-results-oct.xlsx', 'original_name' => 'Test Results October.xlsx', 'path' => 'files/test-results-oct.xlsx', 'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'size' => 450000, 'type' => 'spreadsheet', 'project_id' => $project->id, 'task_id' => $t10->id, 'uploaded_by' => $linh->id]);
        File::create(['name' => 'design-tokens.json', 'original_name' => 'Design Tokens Export.json', 'path' => 'files/design-tokens.json', 'mime_type' => 'application/json', 'size' => 32000, 'type' => 'document', 'project_id' => $project->id, 'task_id' => $t2->id, 'uploaded_by' => $sarah->id]);
        File::create(['name' => 'db-migration-plan.md', 'original_name' => 'Database Migration Plan.md', 'path' => 'files/db-migration-plan.md', 'mime_type' => 'text/markdown', 'size' => 18000, 'type' => 'document', 'project_id' => $project->id, 'task_id' => $t11->id, 'uploaded_by' => $minh->id]);
    }
}
