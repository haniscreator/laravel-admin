<?php

namespace App\Actions\Item;

use App\Services\ItemService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ItemImportAction
{
    protected ItemService $service;

    public function __construct(ItemService $service)
    {
        $this->service = $service;
    }

    public function handle(Request $request): int
    {
        $request->validate([
            'csv' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv');
        $userId = auth()->id();

        // Log::debug('Invalid row structure', ['file' => $file]);
        // return 1;
        return $this->service->importCsv($file, $userId);
    }
}
