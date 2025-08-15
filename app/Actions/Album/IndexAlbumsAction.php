<?php
namespace App\Actions\Album;

use Illuminate\Http\Request;
use App\Services\AlbumService;

class IndexAlbumsAction
{
    protected AlbumService $service;

    public function __construct(AlbumService $service)
    {
        $this->service = $service;
    }

    public function handle(Request $request)
    {
        $albums = $this->service->getList($request);

        return [
            'albums' => $albums,
            'filters' => $request->only('keyword', 'sort', 'direction', 'status'),
        ];
    }
}
