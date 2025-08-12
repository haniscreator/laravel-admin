<?php
namespace App\Actions\Album;

use App\Services\AlbumService;
use Illuminate\Http\Request;
use App\Models\Album;

class UpdateAlbumAction
{
    protected AlbumService $service;

    public function __construct(AlbumService $service)
    {
        $this->service = $service;
    }

    public function handle(Request $request, Album $album)
    {
        $data = $request->validated();
        $image = $request->file('image');
        return $this->service->updateAlbum($album, $data, $image);
    }
}
