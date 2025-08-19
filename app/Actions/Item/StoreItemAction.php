<?php

namespace App\Actions\Item;

use App\Services\ItemService;
use Illuminate\Http\Request;

class StoreItemAction
{
    protected ItemService $service;

    public function __construct(ItemService $service)
    {
        $this->service = $service;
    }

    public function handle(Request $request)
    {
        $data = $request->validated();

        $image = $request->file('media_url');
        $userId = $request->user()->id ?? null;

        return $this->service->store($data, $image, $userId);
    }
}
