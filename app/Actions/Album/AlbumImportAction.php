<?php

namespace App\Actions\Album;

use App\Services\AlbumService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlbumImportAction
{
    protected AlbumService $service;

    public function __construct(AlbumService $service)
    {
        $this->service = $service;
    }

    public function handle(Request $request): int
{
    $request->validate([
        'csv' => 'required|file|mimes:csv,txt|max:2048',
    ]);

    $file   = $request->file('csv');
    $userId = \Auth::id();

    // Service throws on bad header/empty CSV/etc.
    return $this->service->importCsv($file, $userId);
}

}
