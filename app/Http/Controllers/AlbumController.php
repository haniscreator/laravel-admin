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

        // Handle search
        if ($request->filled('keyword')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->keyword . '%')
                ->orWhere('description', 'like', '%' . $request->keyword . '%')
                ->orWhere('location', 'like', '%' . $request->keyword . '%');
            });
        }

        // Handle sorting
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');

        if (in_array($sort, ['id', 'name', 'description', 'location']) &&
            in_array($direction, ['asc', 'desc'])) {
            $query->orderBy($sort, $direction);
        }

        // Paginate and preserve all filters
        $albums = $query->paginate(10)->appends($request->only('keyword', 'sort', 'direction'));

        return Inertia::render('Albums/Index', [
            'albums' => $albums,
            'filters' => $request->only('keyword', 'sort', 'direction'),
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
            'description' => 'required|string',
            'location' => 'required|string',
            'keyword' => 'required|string',
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
            'description' => 'required|string',
            'location' => 'required|string',
            'keyword' => 'required|string',
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
