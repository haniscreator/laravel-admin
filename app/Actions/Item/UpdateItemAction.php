<?php

namespace App\Actions\Item;

use App\Models\Item;
use App\Services\ItemService;
use Illuminate\Http\Request;

class UpdateItemAction
{
    protected ItemService $service;

    public function __construct(ItemService $service)
    {
        $this->service = $service;
    }

    public function handle(Request $request, Item $item)
    {
        $data = $request->validated();

        $image = $request->file('image');
        $userId = $request->user()->id ?? null;

        return $this->service->update($item, $data, $image, $userId);
    }
}
