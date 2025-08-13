<?php

namespace App\Services;

use App\Models\Album;
use App\Models\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AlbumService
{
    protected $album;

    public function __construct(Album $album)
    {
        $this->album = $album;
    }
    
    /**
     * Search/sort/paginate album query.
     */
    public function getPaginated($request, $perPage = 10)
    {
        $query = $this->album->withCount('items');

        if ($request->filled('keyword')) {
            $kw = $request->keyword;
            $query->where(function ($q) use ($kw) {
                $q->where('name', 'like', '%' . $kw . '%')
                  ->orWhere('description', 'like', '%' . $kw . '%')
                  ->orWhere('location', 'like', '%' . $kw . '%')
                  ->orWhere('keyword', 'like', '%' . $kw . '%');
            });
        }

        if ($request->filled('status') && $request->status !== '') {
            // allow '0' or '1' - treat empty as all
            $query->where('status', $request->status);
        }

        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');

        if (in_array($sort, ['id','name','description','location']) && in_array($direction, ['asc','desc'])) {
            $query->orderBy($sort, $direction);
        }

        return $query->paginate($perPage)
            ->appends($request->only('keyword', 'sort', 'direction', 'status'));
    }

    /**
     * Create album and optional image.
     */
    public function createAlbum(array $data): Album
    {

        $data = $this->prepareAlbumData($data);
        
        return $this->album->create($data);
    }

    /**
     * Update album and optionally replace image.
     */
    public function updateAlbum(Album $album, array $data, ?UploadedFile $image = null)
    {

        $data = $this->prepareAlbumData($data);

        return $album->update($data);

    }

    public function deleteAlbum(Album $album)
    {
        // Delete all album images (files + DB records)
        $this->deleteAlbumImages($album);

        // Delete album itself
        $album->delete();
    }

    public function toggleStatus(Album $album)
    {
        $album->status = !$album->status;
        $album->save();
        return $album;
    }

    public function getAlbumForEdit(Album $album)
    {
        $image = Image::where('parent_id', $album->id)->where('type', 'album')->first();
        return [
            'album' => $album,
            'image' => $image,
        ];
    }

    // public function storeImage(int $parentId, UploadedFile $image, int $user_id, string $type = 'album')
    // {
    //     $filename = time() . '.' . $image->getClientOriginalExtension();
    //     $path = $image->storeAs('images/upload', $filename, 'public');

    //     return Image::create([
    //         'parent_id' => $parentId,
    //         'path' => $path,
    //         'type' => $type,
    //         'created_by' => $user_id,
    //     ]);
    // }

    public function storeImage(int $parentId, UploadedFile $image, int $user_id, string $type = 'album')
    {
        // Delete existing images for this parent/type
        $existing = Image::where('parent_id', $parentId)
                        ->where('type', $type)
                        ->get();

        foreach ($existing as $row) {
            if (Storage::disk('public')->exists($row->path)) {
                Storage::disk('public')->delete($row->path);
            }
            $row->delete();
        }

        // Store new image file
        $filename = time() . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs('images/upload', $filename, 'public');

        // Create new Image record
        return Image::create([
            'parent_id' => $parentId,
            'path' => $path,
            'type' => $type,
            'created_by' => $user_id,
        ]);
    }


    protected function normalizeKeywords(string $raw): string
    {
        $parts = preg_split('/[\s,]+/', $raw, -1, PREG_SPLIT_NO_EMPTY);
        $tags = array_unique(array_map('trim', $parts));
        return implode(', ', $tags);
    }

    protected function prepareAlbumData(array $data): array
    {
        if (!empty($data['keyword'])) {
            $data['keyword'] = $this->normalizeKeywords($data['keyword']);
        }

        return $data;
    }

    public function deleteAlbumImages(Album $album, string $type = 'album')
    {
        $existing = Image::where('parent_id', $album->id)
            ->where('type', $type)
            ->get();

        foreach ($existing as $row) {
            if (Storage::disk('public')->exists($row->path)) {
                Storage::disk('public')->delete($row->path);
            }
        }

        // Remove DB records for images
        Image::where('parent_id', $album->id)
            ->where('type', $type)
            ->delete();
    }
}
