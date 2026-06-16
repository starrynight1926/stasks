<?php

namespace App\Http\Controllers;

use App\Models\Department;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::withCount('members')->get();

        return view('departments', compact('departments'));
    }
}
