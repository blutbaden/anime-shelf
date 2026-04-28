<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagsController extends Controller
{
    public function index()
    {
        $tags = Tag::withCount('animes')->orderBy('name')->paginate(30);
        return view('admin.tags.index', compact('tags'));
    }

    public function create()
    {
        return view('admin.tags.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:100|unique:tags,name',
        ]);

        $tag = Tag::create($validated);
        AuditLog::record('tag.created', $tag, [], ['name' => $tag->name]);

        return back()->with('success', 'Tag created successfully.');
    }

    public function edit($id)
    {
        $tag = Tag::findOrFail($id);
        return view('admin.tags.edit', compact('tag'));
    }

    public function update(Request $request, $id)
    {
        $tag       = Tag::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|max:100|unique:tags,name,' . $id,
        ]);
        $old = $tag->only(['name']);
        $tag->update($validated);
        AuditLog::record('tag.updated', $tag, $old, $tag->only(['name']));

        return back()->with('success', 'Tag updated successfully.');
    }

    public function destroy($id)
    {
        $tag = Tag::findOrFail($id);
        AuditLog::record('tag.deleted', $tag, ['name' => $tag->name], []);
        $tag->delete();

        return redirect('/admin/tags')->with('deleted_tag', 'Tag has been deleted.');
    }
}
