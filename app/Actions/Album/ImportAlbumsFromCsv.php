<?php

namespace App\Actions\Album;

use App\Services\AlbumService;
use League\Csv\Reader;


class ImportAlbumsFromCsv
{
    protected $service;

    public function __construct(AlbumService $service)
    {
        $this->service = $service;
    }

    public function handle(string $filePath): int
    {
        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0);

        $count = 0;
        foreach ($csv->getRecords() as $row) {
            $this->service->createAlbumFromArray([
                'name'        => $row['name'] ?? '',
                'description' => $row['description'] ?? '',
                'keyword'     => $row['keyword'] ?? '',
                'location'    => $row['location'] ?? '',
                'status'      => $row['status'] ?? 0,
            ]);
            $count++;
        }

        return $count;
    }
}
