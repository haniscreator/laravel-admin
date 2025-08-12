<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Actions\Album\IndexAlbumsAction;
use App\Actions\Album\StoreAlbumAction;
use App\Actions\Album\GetAlbumForEditAction;
use App\Actions\Album\UpdateAlbumAction;
use App\Actions\Album\DeleteAlbumAction;
use App\Actions\Album\ToggleAlbumStatusAction;
use App\Http\Requests\StoreAlbumRequest;
use App\Http\Requests\UpdateAlbumRequest;
use App\Models\Album;

class AlbumController extends Controller
{
    public function index(Request $request, IndexAlbumsAction $action)
    {
        $result = $action->handle($request);

        return Inertia::render('Albums/Index', $result);
    }

    public function create()
    {
        return Inertia::render('Albums/Create');
    }

    public function store(StoreAlbumRequest $request, StoreAlbumAction $action)
    {
        $action->handle($request);

        return redirect()->route('albums.index')->with('success', 'Album created successfully.');
    }

    public function edit(Album $album, GetAlbumForEditAction $action)
    {
        $data = $action->handle($album);

        return Inertia::render('Albums/Edit', $data);
    }

    public function update(UpdateAlbumRequest $request, Album $album, UpdateAlbumAction $action)
    {
        $action->handle($request, $album);

        return redirect()->route('albums.index')->with('success', 'Album updated successfully.');
    }

    public function destroy(Album $album, DeleteAlbumAction $action)
    {
        $action->handle($album);

        return redirect()->route('albums.index')->with('success', 'Album deleted successfully.');
    }

    public function toggleStatus(Request $request, Album $album, ToggleAlbumStatusAction $action)
    {
        $action->handle($album);

        // Because your UI expects an Inertia response sometimes, returning back is safe:
        return redirect()->back()->with('success', 'Status updated.');
    }
}
