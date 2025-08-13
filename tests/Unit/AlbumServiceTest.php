<?php

namespace Tests\Unit;

use App\Models\Album;
use App\Services\AlbumService;
use App\Services\FileStorageService;
use Mockery;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\Facades\Storage;

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

    public function test_store_image_deletes_old_and_creates_new()
    {
        // Mock UploadedFile
        $imageMock = Mockery::mock(\Illuminate\Http\UploadedFile::class);
        $imageMock->shouldReceive('getClientOriginalExtension')->once()->andReturn('jpg');
        $imageMock->shouldReceive('storeAs')
            ->once()
            ->withArgs(function ($dir, $filename, $disk) {
                return $dir === 'images/upload' && $disk === 'public';
            })
            ->andReturn('images/upload/fake-image.jpg');

        // Mock existing Image records
        $existingImageMock = Mockery::mock();
        $existingImageMock->path = 'images/upload/old-image.jpg';
        $existingImageMock->shouldReceive('delete')->once();

        // Mock Image model static calls
        $imageModelMock = Mockery::mock('alias:' . \App\Models\Image::class);
        $imageModelMock->shouldReceive('where')
            ->with('parent_id', 123)
            ->andReturnSelf();
        $imageModelMock->shouldReceive('where')
            ->with('type', 'album')
            ->andReturnSelf();
        $imageModelMock->shouldReceive('get')
            ->andReturn([$existingImageMock]);
        $imageModelMock->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($data) {
                $this->assertEquals(123, $data['parent_id']);
                $this->assertEquals('images/upload/fake-image.jpg', $data['path']);
                $this->assertEquals('album', $data['type']);
                $this->assertEquals(99, $data['created_by']);
                return true;
            }))
            ->andReturn((object) ['id' => 999, 'path' => 'images/upload/fake-image.jpg']);

        // Mock Storage facade
        $storageMock = Mockery::mock();
        $storageMock->shouldReceive('exists')
            ->once()
            ->with('images/upload/old-image.jpg')
            ->andReturn(true);
        $storageMock->shouldReceive('delete')
            ->once()
            ->with('images/upload/old-image.jpg')
            ->andReturn(true);

        Storage::shouldReceive('disk')
            ->with('public')
            ->andReturn($storageMock);

        // Create service instance
        $service = new \App\Services\AlbumService(Mockery::mock(\App\Models\Album::class));

        // Call method
        $result = $service->storeImage(123, $imageMock, 99, 'album');

        // Assert new image is returned correctly
        $this->assertEquals('images/upload/fake-image.jpg', $result->path);
        $this->assertEquals(999, $result->id);
    }

    public function test_update_album()
{
    $expectedData = [
        'name' => 'nam name updated',
        'description' => 'desc desc updated',
        'location' => 'mdy',
        'keyword' => 'keyword1, keyword2',
        'status' => '1'
    ];

    // Mock Album instance
    $mockAlbumInstance = \Mockery::mock(\App\Models\Album::class);

    // Expect prepareAlbumData to be called internally and return same $expectedData
    $service = \Mockery::mock(\App\Services\AlbumService::class)
        ->makePartial()
        ->shouldAllowMockingProtectedMethods();

    $service->shouldReceive('prepareAlbumData')
        ->once()
        ->with($expectedData)
        ->andReturn($expectedData);

    // Expect update on the album instance
    $mockAlbumInstance
        ->shouldReceive('update')
        ->once()
        ->with($expectedData)
        ->andReturn(true);

    $result = $service->updateAlbum($mockAlbumInstance, $expectedData);

    $this->assertTrue($result);
}


public function test_delete_album_calls_image_deletion_and_album_delete()
{
    $mockAlbumInstance = Mockery::mock(\App\Models\Album::class);
    $mockAlbumInstance->shouldReceive('delete')->once()->andReturn(true);

    $serviceMock = Mockery::mock(\App\Services\AlbumService::class)->makePartial();
    $serviceMock->shouldReceive('deleteAlbumImages')->once()->with($mockAlbumInstance);

    $serviceMock->deleteAlbum($mockAlbumInstance);

    // Mark this test as having performed assertions
    $this->addToAssertionCount(1);
}





}
