<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\File;
use App\Models\Task;
use App\Support\TaskOwnership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CommentController extends Controller
{
    public function store(Request $request, Task $task)
    {
        $validated = $request->validate([
            'body' => 'required|string|max:2000',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|max:20480',
        ]);

        $comment = $task->comments()->create([
            'body' => $validated['body'],
            'author_name' => session('user_name', 'Unknown'),
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $uploadedFile) {
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
                    'task_id' => $task->id,
                    'comment_id' => $comment->id,
                ]);
            }
        }

        return redirect()->route('tasks.show', $task)->with('success', 'Comment added.');
    }

    public function destroy(Comment $comment)
    {
        $task = $comment->task;
        if ($task) {
            TaskOwnership::abortIfNotOwner($task);
        }

        foreach ($comment->files as $file) {
            Storage::disk('public')->delete($file->path);
        }
        $comment->files()->delete();
        $taskId = $comment->task_id;
        $comment->delete();

        return redirect()->route('tasks.show', $taskId)->with('success', 'Comment deleted.');
    }
}
