<?php

namespace App\Actions\Item;

use App\Services\ItemService;
use Illuminate\Http\Request;

class ItemImportAction
{
    protected ItemService $service;

    public function __construct(ItemService $service)
    {
        $this->service = $service;
    }

    public function handle(Request $request): int
    {
        $file = $request->file('file');
        $userId = $request->user()->id ?? null;

        return $this->service->importCsv($file, $userId);
    }
}
