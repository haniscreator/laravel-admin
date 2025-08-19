<?php

namespace App\Actions\Item;

use App\Models\Item;
use App\Services\ItemService;

class DeleteItemAction
{
    protected ItemService $service;

    public function __construct(ItemService $service)
    {
        $this->service = $service;
    }

    public function handle(Item $item)
    {
        return $this->service->delete($item);
    }
}
