<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;


class AlbumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Album::query();

        if ($request->filled('keyword')) {
            $query->where('name', 'like', '%' . $request->keyword . '%')
                ->orWhere('description', 'like', '%' . $request->keyword . '%')
                ->orWhere('location', 'like', '%' . $request->keyword . '%');
        }

        $albums = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();
        
        return Inertia::render('Albums/Index', [
            'albums' => $albums,
            'filters' => $request->only('keyword'),
        ]);
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
       return Inertia::render('Albums/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string',
            'keyword' => 'nullable|string',
            'status' => 'boolean',
        ]);

        Album::create($validated);

        return redirect()->route('albums.index')->with('success', 'Album created successfully.');

    }

    /**
     * Display the specified resource.
     */
    public function show(Album $album)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Album $album)
    {
        return Inertia::render('Albums/Edit', [
            'album' => $album,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Album $album)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string',
            'keyword' => 'nullable|string',
            'status' => 'boolean',
        ]);

        $album->update($validated);

        return redirect()->route('albums.index')->with('success', 'Album updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Album $album)
    {
        $album->delete();

        return redirect()->route('albums.index')->with('success', 'Album deleted successfully.');
    }

}
