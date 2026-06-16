<?php

namespace App\Http\Controllers;

use App\Models\File;

class FileController extends Controller
{
    public function index()
    {
        $files = File::with('uploader', 'project', 'task')->latest()->get();

        return view('files', compact('files'));
    }
}
