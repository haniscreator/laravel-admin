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
        $image = $request->file('image');
        return $this->service->createAlbum($data, $image);
    }
}
