<?php

namespace App\Actions\Album;

use App\Services\AlbumService;
use Illuminate\Http\Request;

class StoreAlbumAction
{
    protected AlbumService $service;

    public function __construct(AlbumService $service)
    {
        $this->service = $service;
    }

    public function handle(Request $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $image = $request->file('image');

        $album = $this->service->createAlbum($data);

        if ($image) {
            $this->service->storeImage($album->id, $image, auth()->id(), 'album');
        }

        return $album;
    }
}
