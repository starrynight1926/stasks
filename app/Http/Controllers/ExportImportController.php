<?php

namespace App\Http\Controllers;

use App\Exports\TasksExport;
use App\Exports\TeamMembersExport;
use App\Exports\DepartmentsExport;
use App\Imports\TasksImport;
use App\Imports\TeamMembersImport;
use App\Imports\DepartmentsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportImportController extends Controller
{
    public function exportTasks()
    {
        return Excel::download(new TasksExport, 'tasks_' . date('Y-m-d') . '.xlsx');
    }

    public function exportMembers()
    {
        return Excel::download(new TeamMembersExport, 'team_members_' . date('Y-m-d') . '.xlsx');
    }

    public function exportDepartments()
    {
        return Excel::download(new DepartmentsExport, 'departments_' . date('Y-m-d') . '.xlsx');
    }

    public function importTasks(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv|max:10240']);

        try {
            Excel::import(new TasksImport, $request->file('file'));
            return redirect()->back()->with('success', 'Import tasks thành công!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = collect($failures)->map(fn($f) => "Dòng {$f->row()}: {$f->errors()[0]}")->take(5)->toArray();
            return redirect()->back()->withErrors($errors);
        }
    }

    public function importMembers(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv|max:10240']);

        try {
            Excel::import(new TeamMembersImport, $request->file('file'));
            return redirect()->back()->with('success', 'Import thành viên thành công!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = collect($failures)->map(fn($f) => "Dòng {$f->row()}: {$f->errors()[0]}")->take(5)->toArray();
            return redirect()->back()->withErrors($errors);
        }
    }

    public function importDepartments(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv|max:10240']);

        try {
            Excel::import(new DepartmentsImport, $request->file('file'));
            return redirect()->back()->with('success', 'Import phòng ban thành công!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = collect($failures)->map(fn($f) => "Dòng {$f->row()}: {$f->errors()[0]}")->take(5)->toArray();
            return redirect()->back()->withErrors($errors);
        }
    }
}
