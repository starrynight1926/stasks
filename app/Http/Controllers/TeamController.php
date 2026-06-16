<?php

namespace App\Http\Controllers;

use App\Models\TeamMember;

class TeamController extends Controller
{
    public function index()
    {
        $members = TeamMember::with('department')->get();

        return view('teams', compact('members'));
    }
}
