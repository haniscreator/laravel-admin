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

        if ($request->has('keyword')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->keyword}%")
                ->orWhere('description', 'like', "%{$request->keyword}%")
                ->orWhere('keyword', 'like', "%{$request->keyword}%");
            });
        }

        /** @var \App\Models\User $user */
        $user = auth()->user();

        if ($user && $user->hasRole('Entry')) {
            $query->where('created_by', $user->id);
        }

        $albums = $query->paginate(10);

        return Inertia::render('Albums/Index', [  // <-- Note the Inertia render and Vue page path
            'albums' => $albums,
            'filters' => $request->only(['keyword']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Album $album)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Album $album)
    {
        //
    }
}
