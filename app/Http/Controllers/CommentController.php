<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Task;
use App\Models\TeamMember;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Task $task)
    {
        $validated = $request->validate([
            'body' => 'required|string|max:2000',
            'team_member_id' => 'required|exists:team_members,id',
        ]);

        $task->comments()->create($validated);

        return redirect()->route('tasks.show', $task)->with('success', 'Comment added.');
    }

    public function destroy(Comment $comment)
    {
        $taskId = $comment->task_id;
        $comment->delete();

        return redirect()->route('tasks.show', $taskId)->with('success', 'Comment deleted.');
    }
}
