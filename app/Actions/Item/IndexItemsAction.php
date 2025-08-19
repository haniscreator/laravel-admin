<?php

namespace App\Actions\Item;

use App\Services\AlbumService;
use App\Services\ItemService;
use Illuminate\Http\Request;

class IndexItemsAction
{
    protected $itemService;

    protected $albumService;

    public function __construct(ItemService $itemService, AlbumService $albumService)
    {
        $this->itemService = $itemService;
        $this->albumService = $albumService;
    }

    public function handle(Request $request): array
    {

        $items = $this->itemService->getList($request);
        $albums = $this->albumService->getActiveForDropdown();

        return [
            'items' => $items,
            'albums' => $albums,
            'filters' => $request->only(['keyword', 'album_id', 'sort', 'direction', 'status']),
        ];

    }
}
