<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Album;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Item::with('album'); // eager load album

        // Filter by album_id if provided
        if ($request->filled('album_id')) {
            $query->where('album_id', $request->album_id);
        }

        // Handle search keyword filter
        if ($request->filled('keyword')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->keyword . '%')
                ->orWhere('description', 'like', '%' . $request->keyword . '%')
                ->orWhere('keyword', 'like', '%' . $request->keyword . '%');
            });
        }

        // Get sort field & direction with validation
        $allowedSortFields = ['id', 'name', 'description'];
        $sortField = $request->get('sort', 'id');
        $sortDirection = $request->get('direction', 'asc');

        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'id';
        }

        if (!in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        // Apply sorting
        $query->orderBy($sortField, $sortDirection);

        // Paginate and transform results
        $items = $query->paginate(10)
            ->through(fn ($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'description' => $item->description,
                'keyword' => $item->keyword,
                'status' => $item->status,
                'album_name' => optional($item->album)->name,
            ])
            ->appends($request->only('keyword', 'album_id', 'sort', 'direction'));

        // Get albums for dropdown
        $albums = Album::where('status', 1)->select('id', 'name')->get();

        // Pass filters (including sort) back to Vue
        return Inertia::render('Items/Index', [
            'items' => $items,
            'albums' => $albums,
            'filters' => $request->only('keyword', 'album_id', 'sort', 'direction'),
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $albums = Album::where('status', 1)->select('id', 'name')->get();
       
        return Inertia::render('Items/Create', [
            'albums' => $albums,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'album_id'    => 'required|exists:albums,id',
            'name'        => 'required|string|max:255',
            'description' => 'required|string',
            'keyword'     => 'required|string',
            'media_url'   => 'required|file|mimetypes:audio/mpeg,audio/wav,audio/x-wav,audio/mp3',
            'status'      => 'required|boolean',
        ]);

        if (!empty($validated['keyword'])) {
            // Split by comma and/or space
            $parts = preg_split('/[\s,]+/', $validated['keyword'], -1, PREG_SPLIT_NO_EMPTY);

            // Remove duplicates and trim
            $tags = array_unique(array_map('trim', $parts));

            // Join into a comma-separated string
            $validated['keyword'] = implode(', ', $tags);
        }

        // handle file upload
        $mediaFile = $request->file('media_url');
        
        if ($mediaFile) {
            $mediaPath = $mediaFile->storeAs(
                'media',
                time() . '.' . $mediaFile->getClientOriginalExtension(),
                'public'
            );
        } else {
            return back()->withErrors(['media_url' => 'Media file is missing.']);
        }
        // create item
        Item::create([
            ...$validated,
            'media_url' => $mediaPath,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('items.index')->with('success', 'Item created successfully.');
    }


    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        $albums = Album::where('status', 1)->get(['id', 'name']);
        return Inertia::render('Items/Edit', [
            'item' => $item,
            'albums' => $albums,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'album_id' => 'required|exists:albums,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'keyword' => 'required|string',
            'media_url' => 'nullable|file|mimes:mp3,wav,ogg',
            'status' => 'required|boolean',
        ]);

        // Normalize keyword input (deduplicate and trim)
        if (!empty($validated['keyword'])) {
            $parts = preg_split('/[\s,]+/', $validated['keyword'], -1, PREG_SPLIT_NO_EMPTY);
            $tags = array_unique(array_map('trim', $parts));
            $validated['keyword'] = implode(', ', $tags);
        }

        // Update basic fields
        $item->album_id = $validated['album_id'];
        $item->name = $validated['name'];
        $item->description = $validated['description'];
        $item->keyword = $validated['keyword'];
        $item->status = $validated['status'];

        // If a new media file is uploaded, handle file replacement
        if ($request->hasFile('media_url')) {
            // Delete the old media file if it exists
            if ($item->media_url && Storage::disk('public')->exists($item->media_url)) {
                Storage::disk('public')->delete($item->media_url);
            }

            // Upload the new media file
            $mediaFile = $request->file('media_url');
            $filename = time() . '.' . $mediaFile->getClientOriginalExtension();
            $path = $mediaFile->storeAs('media', $filename, 'public');

            // Save the new path
            $item->media_url = $path;
        }

        $item->save();

        return redirect()->route('items.index')->with('success', 'Item updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        // Optional: Delete media file if needed
        if ($item->media_url && Storage::disk('public')->exists($item->media_url)) {
            Storage::disk('public')->delete($item->media_url);
        }

        $item->delete();

        return redirect()->route('items.index')->with('success', 'Item deleted successfully.');

    }

    /**
     * Topggle the Status.
     */
    public function toggleStatus(Request $request, Item $item)
    {
        $item->status = !$item->status;
        $item->save();

        return redirect()->back()->with('success', 'Item status updated.');
    }

}
