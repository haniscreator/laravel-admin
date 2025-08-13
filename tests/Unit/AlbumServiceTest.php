<?php

namespace Tests\Unit;

use App\Models\Album;
use App\Services\AlbumService;
use App\Services\FileStorageService;
use Mockery;
use PHPUnit\Framework\TestCase;

class AlbumServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_create_album()
{
    $mockAlbumModel = \Mockery::mock(Album::class);
    $expectedData = [
        'name' => 'nam name',
        'description' => 'desc desc',
        'location' => 'ygn',
        'keyword' => 'keyword1, keyword2',
        'status' => '1'
    ];

    $mockAlbumInstance = new Album($expectedData);

    $mockAlbumModel
        ->shouldReceive('create')
        ->once()
        ->with($expectedData)
        ->andReturn($mockAlbumInstance);

    $service = new AlbumService($mockAlbumModel);
    $result = $service->createAlbum($expectedData);

    $this->assertSame($mockAlbumInstance, $result);
}



}
