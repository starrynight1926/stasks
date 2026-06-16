<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::withCount('tasks')->get();

        return view('tags', compact('tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:tags,name',
            'color' => 'required|string|max:7',
        ]);

        Tag::create($validated);

        return redirect()->route('tags')->with('success', 'Tag created successfully.');
    }

    public function update(Request $request, Tag $tag)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:tags,name,' . $tag->id,
            'color' => 'required|string|max:7',
        ]);

        $tag->update($validated);

        return redirect()->route('tags')->with('success', 'Tag updated successfully.');
    }

    public function destroy(Tag $tag)
    {
        $tag->tasks()->detach();
        $tag->delete();

        return redirect()->route('tags')->with('success', 'Tag deleted successfully.');
    }
}
