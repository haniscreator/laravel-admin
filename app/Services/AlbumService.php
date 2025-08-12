<?php

namespace App\Services;

use App\Models\Album;
use App\Models\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AlbumService
{
    /**
     * Search/sort/paginate album query.
     */
    public function getPaginated($request, $perPage = 10)
    {
        $query = Album::withCount('items');

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
    public function createAlbum(array $data, ?UploadedFile $image = null)
    {
        // Normalize keywords => "a, b, c"
        if (!empty($data['keyword'])) {
            $data['keyword'] = $this->normalizeKeywords($data['keyword']);
        }

        $album = Album::create($data);

        if ($image) {
            $this->storeImage($album->id, $image, 'album');
        }

        return $album;
    }

    /**
     * Update album and optionally replace image.
     */
    public function updateAlbum(Album $album, array $data, ?UploadedFile $image = null)
    {
        if (!empty($data['keyword'])) {
            $data['keyword'] = $this->normalizeKeywords($data['keyword']);
        }

        $album->update($data);

        if ($image) {
            // Delete existing images and files for this album
            $existing = Image::where('parent_id', $album->id)->where('type', 'album')->get();
            foreach ($existing as $row) {
                if (Storage::disk('public')->exists($row->path)) {
                    Storage::disk('public')->delete($row->path);
                }
            }
            Image::where('parent_id', $album->id)->where('type', 'album')->delete();

            // store new one
            $this->storeImage($album->id, $image, 'album');
        }

        return $album;
    }

    public function deleteAlbum(Album $album)
    {
        // optional: delete images
        $existing = Image::where('parent_id', $album->id)->where('type', 'album')->get();
        foreach ($existing as $row) {
            if (Storage::disk('public')->exists($row->path)) {
                Storage::disk('public')->delete($row->path);
            }
        }
        Image::where('parent_id', $album->id)->where('type', 'album')->delete();

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

    protected function storeImage(int $parentId, UploadedFile $image, string $type = 'album')
    {
        $filename = time() . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs('images/upload', $filename, 'public');

        return Image::create([
            'parent_id' => $parentId,
            'path' => $path,
            'type' => $type,
            'created_by' => auth()->id(),
        ]);
    }

    protected function normalizeKeywords(string $raw): string
    {
        $parts = preg_split('/[\s,]+/', $raw, -1, PREG_SPLIT_NO_EMPTY);
        $tags = array_unique(array_map('trim', $parts));
        return implode(', ', $tags);
    }
}
