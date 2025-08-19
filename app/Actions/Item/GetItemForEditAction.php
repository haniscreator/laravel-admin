<?php

namespace App\Actions\Item;

use App\Models\Item;
use App\Services\ItemService;

class GetItemForEditAction
{
    protected ItemService $service;

    public function __construct(ItemService $service)
    {
        $this->service = $service;
    }

    public function handle(Item $item): array
    {
        return $this->service->getItemForEdit($item);
    }
}
