<?php
namespace App\Actions\Album;

use App\Services\AlbumService;
use App\Models\Album;

class DeleteAlbumAction
{
    protected AlbumService $service;

    public function __construct(AlbumService $service)
    {
        $this->service = $service;
    }

    public function handle(Album $album)
    {
        return $this->service->deleteAlbum($album);
    }
}
