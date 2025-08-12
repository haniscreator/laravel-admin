<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;


class AlbumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Album::withCount('items'); // This adds items_count

        // Handle search
        if ($request->filled('keyword')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->keyword . '%')
                ->orWhere('description', 'like', '%' . $request->keyword . '%')
                ->orWhere('location', 'like', '%' . $request->keyword . '%')
                ->orWhere('keyword', 'like', '%' . $request->keyword . '%');
            });
        }

        // Handle status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
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

        if (!empty($validated['keyword'])) {
            // Split by comma and/or space
            $parts = preg_split('/[\s,]+/', $validated['keyword'], -1, PREG_SPLIT_NO_EMPTY);

            // Remove duplicates and trim
            $tags = array_unique(array_map('trim', $parts));

            // Join into a comma-separated string
            $validated['keyword'] = implode(', ', $tags);
        }


        $createdAlbum = Album::create($validated);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('images/upload', $filename, 'public');

            Image::create([
                'parent_id' => $createdAlbum->id, 
                'path' => $path,
                'type' => 'album',
                'created_by' => auth()->id(),
            ]);
        }


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
        // Load related image (if exists)
        $image = Image::where('parent_id', $album->id)
            ->where('type', 'album')
            ->first();

        return Inertia::render('Albums/Edit', [
            'album' => $album,
            'image' => $image, // add this line
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if (!empty($validated['keyword'])) {
            // Split by comma and/or space
            $parts = preg_split('/[\s,]+/', $validated['keyword'], -1, PREG_SPLIT_NO_EMPTY);

            // Remove duplicates and trim
            $tags = array_unique(array_map('trim', $parts));

            // Join into a comma-separated string
            $validated['keyword'] = implode(', ', $tags);
        }

        $album->update($validated);
        

        if ($request->hasFile('image')) {

            // Get existing images for this album
            $oldImages = Image::where('parent_id', $album->id)
                ->where('type', 'album')
                ->get();

            // Delete physical files
            foreach ($oldImages as $oldImage) {
                if (Storage::disk('public')->exists($oldImage->path)) {
                    Storage::disk('public')->delete($oldImage->path);
                }
            }

            // Delete existing images for this album
            Image::where('parent_id', $album->id)
                ->where('type', 'album')
                ->delete();

            // Upload and store new image
            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('images/upload', $filename, 'public');

            // Create new image record
            Image::create([
                'parent_id' => $album->id,
                'path' => $path,
                'type' => 'album',
                'created_by' => auth()->id(),
            ]);
        }


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

    /**
     * Update the status.
     */
    public function toggleStatus(Request $request, Album $album)
    {
        $album->status = !$album->status; // toggle between 0 and 1
        $album->save();

        return redirect()->back()->with('success', 'Status updated');
    }


}
