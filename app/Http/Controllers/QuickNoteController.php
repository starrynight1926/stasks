<?php

namespace App\Http\Controllers;

use App\Models\QuickNote;
use Illuminate\Http\Request;

class QuickNoteController extends Controller
{
    public function index()
    {
        return view('quick-tasks');
    }

    private function scope()
    {
        return QuickNote::query()->where('member_id', session('member_id'));
    }

    public function list()
    {
        return response()->json(
            $this->scope()->orderBy('id')->get()->map(fn($t) => $this->shape($t))
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'    => 'required|string|max:255',
            'iso'      => 'required|date',
            'status'   => 'nullable|in:todo,doing,done,partial,blocked',
            'project'  => 'nullable|string|max:100',
            'priority' => 'nullable|string|max:20',
            'note'     => 'nullable|string',
            'subtasks' => 'nullable|array',
            'subtasks.*.id'    => 'required',
            'subtasks.*.title' => 'required|string|max:255',
            'subtasks.*.done'  => 'required|boolean',
        ]);
        $data['status'] = $data['status'] ?? 'todo';
        $data['member_id'] = session('member_id');
        $t = QuickNote::create($data);
        return response()->json($this->shape($t));
    }

    public function update(Request $request, QuickNote $quickNote)
    {
        abort_if($quickNote->member_id !== session('member_id'), 403);
        $data = $request->validate([
            'title'    => 'sometimes|string|max:255',
            'iso'      => 'sometimes|date',
            'status'   => 'sometimes|in:todo,doing,done,partial,blocked',
            'project'  => 'nullable|string|max:100',
            'priority' => 'nullable|string|max:20',
            'note'     => 'nullable|string',
            'subtasks' => 'nullable|array',
            'subtasks.*.id'    => 'required',
            'subtasks.*.title' => 'required|string|max:255',
            'subtasks.*.done'  => 'required|boolean',
        ]);
        $quickNote->update($data);
        return response()->json($this->shape($quickNote->fresh()));
    }

    public function carry(Request $request, QuickNote $quickNote)
    {
        abort_if($quickNote->member_id !== session('member_id'), 403);
        $data = $request->validate(['iso' => 'nullable|date']);
        $target = $data['iso'] ?? now()->toDateString();
        if ($quickNote->carried_to_id) {
            $existing = QuickNote::find($quickNote->carried_to_id);
            if ($existing) return response()->json(['original' => $this->shape($quickNote), 'clone' => $this->shape($existing)]);
        }
        $clone = QuickNote::create([
            'member_id' => session('member_id'),
            'title'     => $quickNote->title,
            'iso'       => $target,
            'status'    => 'todo',
            'project'   => $quickNote->project,
            'priority'  => $quickNote->priority,
            'note'      => $quickNote->note,
            'subtasks'  => ($quickNote->subtasks ?? []) ? array_map(fn($s)=>['id'=>$s['id'] ?? uniqid(),'title'=>$s['title'],'done'=>false], $quickNote->subtasks) : [],
        ]);
        $quickNote->update(['carried_to_id' => $clone->id]);
        return response()->json(['original' => $this->shape($quickNote->fresh()), 'clone' => $this->shape($clone)]);
    }

    public function destroy(QuickNote $quickNote)
    {
        abort_if($quickNote->member_id !== session('member_id'), 403);
        $quickNote->delete();
        return response()->json(['ok' => true]);
    }

    private function shape(QuickNote $t): array
    {
        return [
            'id'       => $t->id,
            'title'    => $t->title,
            'iso'      => $t->iso?->format('Y-m-d'),
            'status'   => $t->status,
            'project'  => $t->project,
            'priority' => $t->priority,
            'note'     => $t->note,
            'subtasks' => $t->subtasks ?? [],
            'carried_to_id' => $t->carried_to_id,
        ];
    }
}
