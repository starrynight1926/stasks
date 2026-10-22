<?php

namespace App\Http\Controllers;

use App\Models\QuickNote;
use App\Models\TeamMember;
use Illuminate\Http\Request;

class QuickNoteController extends Controller
{
    public function index()
    {
        $me = $this->currentMember();
        $visibleIds = $me ? $me->visibleMemberIds() : [session('member_id')];
        $visibleUsers = $me
            ? TeamMember::whereIn('id', $visibleIds)->orderBy('name')->get(['id', 'name', 'level'])
            : collect();

        return view('quick-tasks', compact('visibleUsers'));
    }

    private function currentMember(): ?TeamMember
    {
        $id = session('member_id');
        return $id ? TeamMember::find($id) : null;
    }

    private function visibleIds(): array
    {
        $me = $this->currentMember();
        if (!$me) return [];
        return $me->visibleMemberIds();
    }

    private function canAccess(QuickNote $t): bool
    {
        if (session('is_admin')) return $t->member_id === null;
        return in_array($t->member_id, $this->visibleIds(), true);
    }

    private function targetMemberId(Request $request): int|null
    {
        $me = $this->currentMember();
        $requested = $request->input('user_id') ? (int) $request->input('user_id') : null;
        if ($requested && in_array($requested, $this->visibleIds(), true)) {
            return $requested;
        }
        return $me?->id ?? session('member_id');
    }

    public function list(Request $request)
    {
        $q = QuickNote::query();
        if (session('is_admin')) {
            $q->whereNull('member_id');
        } else {
            $visible = $this->visibleIds();
            $q->whereIn('member_id', $visible);
            $filter = $request->input('user_id');
            if ($filter && in_array((int)$filter, $visible, true)) {
                $q->where('member_id', (int) $filter);
            }
        }
        return response()->json($q->orderBy('id')->get()->map(fn($t) => $this->shape($t)));
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
        $data['member_id'] = $this->targetMemberId($request);
        $t = QuickNote::create($data);
        return response()->json($this->shape($t));
    }

    public function update(Request $request, QuickNote $quickNote)
    {
        abort_unless($this->canAccess($quickNote), 403);
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
        abort_unless($this->canAccess($quickNote), 403);
        $data = $request->validate(['iso' => 'nullable|date']);
        $target = $data['iso'] ?? now()->toDateString();
        if ($quickNote->carried_to_id) {
            $existing = QuickNote::find($quickNote->carried_to_id);
            if ($existing) return response()->json(['original' => $this->shape($quickNote), 'clone' => $this->shape($existing)]);
        }
        $clone = QuickNote::create([
            'member_id' => $quickNote->member_id,
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
        abort_unless($this->canAccess($quickNote), 403);
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
            'member_id' => $t->member_id,
        ];
    }
}
