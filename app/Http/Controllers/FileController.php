<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Project;
use App\Models\Task;
use App\Models\TeamMember;
use App\Support\TaskOwnership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function index()
    {
        $files = File::with('uploader', 'project', 'task')->latest()->get();
        $projects = Project::all();
        $members = TeamMember::all();

        return view('files', compact('files', 'projects', 'members'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:20480',
            'project_id' => 'nullable|exists:projects,id',
            'uploaded_by' => 'required|exists:team_members,id',
        ]);

        $uploadedFile = $request->file('file');
        $path = $uploadedFile->store('files', 'public');

        $ext = strtolower($uploadedFile->getClientOriginalExtension());
        $type = match (true) {
            in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']) => 'image',
            in_array($ext, ['xls', 'xlsx', 'csv']) => 'spreadsheet',
            default => 'document',
        };

        File::create([
            'name' => $uploadedFile->hashName(),
            'original_name' => $uploadedFile->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $uploadedFile->getMimeType(),
            'size' => $uploadedFile->getSize(),
            'type' => $type,
            'project_id' => $request->project_id,
            'uploaded_by' => $request->uploaded_by,
        ]);

        return redirect()->route('files')->with('success', 'File uploaded successfully.');
    }

    public function show(Request $request, File $file)
    {
        $path = Storage::disk('public')->path($file->path);
        if (!file_exists($path)) {
            abort(404);
        }
        $disposition = $request->boolean('download') ? 'attachment' : 'inline';
        $filename = str_replace(['"', "\r", "\n"], '', $file->original_name);
        return response()->file($path, [
            'Content-Type' => $file->mime_type ?: 'application/octet-stream',
            'Content-Disposition' => $disposition . '; filename="' . $filename . '"; filename*=UTF-8\'\'' . rawurlencode($file->original_name),
        ]);
    }

    public function destroy(Request $request, File $file)
    {
        if ($file->task_id) {
            $task = Task::find($file->task_id);
            if ($task) {
                TaskOwnership::abortIfNotOwner($task);
            }
        }

        Storage::disk('public')->delete($file->path);
        $taskId = $file->task_id;
        $file->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        if ($taskId && $request->headers->get('referer') && str_contains($request->headers->get('referer'), '/tasks/')) {
            return redirect()->route('tasks.show', $taskId)->with('success', 'File deleted.');
        }
        return redirect()->route('files')->with('success', 'File deleted successfully.');
    }

    public function storeForTask(Request $request, Task $task)
    {
        $request->validate([
            'file' => 'required|file|max:20480',
        ]);

        $uploadedFile = $request->file('file');
        $path = $uploadedFile->store('files', 'public');

        $ext = strtolower($uploadedFile->getClientOriginalExtension());
        $type = match (true) {
            in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']) => 'image',
            in_array($ext, ['xls', 'xlsx', 'csv']) => 'spreadsheet',
            default => 'document',
        };

        $file = File::create([
            'name' => $uploadedFile->hashName(),
            'original_name' => $uploadedFile->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $uploadedFile->getMimeType(),
            'size' => $uploadedFile->getSize(),
            'type' => $type,
            'task_id' => $task->id,
            'project_id' => $task->project_id,
        ]);

        return redirect()->route('tasks.show', $task)->with('success', 'File uploaded.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:files,id']);

        $files = File::whereIn('id', $request->ids)->get();
        foreach ($files as $file) {
            Storage::disk('public')->delete($file->path);
            $file->delete();
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => count($request->ids) . ' files deleted.']);
        }
        return redirect()->route('files')->with('success', count($request->ids) . ' files deleted.');
    }
}
