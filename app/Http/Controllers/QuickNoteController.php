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
        ]);
        $quickNote->update($data);
        return response()->json($this->shape($quickNote->fresh()));
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
        ];
    }
}
