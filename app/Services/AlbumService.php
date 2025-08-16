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
    public function getList($request, $perPage = 10)
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

    /*
    public function importCsv(UploadedFile $file, int $userId): int
    {
        $importedCount = 0;

        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            $header = fgetcsv($handle);
            $header = array_map('trim', $header);

            $expected = ['name', 'description', 'keyword', 'location', 'status'];

            if ($header !== $expected) {
                fclose($handle);
                throw new \Exception('Invalid CSV header. Expected: ' . implode(', ', $expected));
            }

            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < count($expected)) {
                    continue;
                }

                $data = array_combine($expected, $row);

                Album::create([
                    'name'         => $data['name'],
                    'description'  => $data['description'],
                    'keyword'      => $data['keyword'],
                    'location'     => $data['location'],
                    'status'       => $data['status'],
                    'created_date' => now(),
                    'created_by'   => $userId,
                ]);

                $importedCount++;
            }

            fclose($handle);
        }

        return $importedCount;
    }
        */

    /*
    public function importCsv(UploadedFile $file, int $userId): int
{
    $importedCount = 0;

    // Open CSV file safely
    if (($handle = fopen($file->getRealPath(), 'r')) === false) {
        throw new \Exception('Unable to open the uploaded CSV file.');
    }

    // Read and clean header row
    $header = fgetcsv($handle);
    if (!$header) {
        fclose($handle);
        throw new \Exception('CSV file is empty or unreadable.');
    }
    $header = array_map('trim', $header);

    // Expected column names
    $expected = ['name', 'description', 'keyword', 'location', 'status'];

    // Validate header strictly
    if ($header !== $expected) {
        fclose($handle);
        throw new \Exception('Invalid CSV header. Expected: ' . implode(', ', $expected));
    }

    // Process each row
    while (($row = fgetcsv($handle)) !== false) {
        // Skip incomplete rows
        if (count($row) < count($expected)) {
            continue;
        }

        $data = array_combine($expected, array_map('trim', $row));

        // Optional: extra per-row validation
        if (empty($data['name']) || empty($data['status'])) {
            continue; // Skip rows with missing required fields
        }

        Album::create([
            'name'         => $data['name'],
            'description'  => $data['description'],
            'keyword'      => $data['keyword'],
            'location'     => $data['location'],
            'status'       => $data['status'],
            'created_date' => now(),
            'created_by'   => $userId,
        ]);

        $importedCount++;
    }

    fclose($handle);

    // No rows imported → still count as valid but let user know
    if ($importedCount === 0) {
        throw new \Exception('No valid albums found in the CSV file.');
    }

    return $importedCount;
}
    */


public function importCsv(UploadedFile $file, int $userId): int
{
    $importedCount = 0;

    // Open CSV file
    if (($handle = fopen($file->getRealPath(), 'r')) === false) {
        throw new \Exception('Unable to open the uploaded CSV file.');
    }

    // Read header row
    $header = fgetcsv($handle);
    if (!$header) {
        fclose($handle);
        throw new \Exception('CSV file is empty or unreadable.');
    }
    $header = array_map('trim', $header);

    // Expected columns
    $expected = ['name', 'description', 'keyword', 'location', 'status'];

    // Strict header check
    if ($header !== $expected) {
        fclose($handle);
        throw new \Exception('Your CSV header is incorrect. Expected columns: ' . implode(', ', $expected));
    }

    // Read each row
    while (($row = fgetcsv($handle)) !== false) {
        // Skip if not enough columns
        if (count($row) < count($expected)) {
            continue;
        }

        $data = array_combine($expected, array_map('trim', $row));

        // Basic per-row validation
        if (!isset($data['name']) || $data['name'] === '' || !isset($data['status']) || $data['status'] === '') {
            continue; // Skip rows with truly missing required fields
        }

        Album::create([
            'name'         => $data['name'],
            'description'  => $data['description'],
            'keyword'      => $data['keyword'],
            'location'     => $data['location'],
            'status'       => $data['status'],
            'created_date' => now(),
            'created_by'   => $userId,
        ]);

        $importedCount++;
    }

    fclose($handle);

    if ($importedCount === 0) {
        throw new \Exception('No valid albums were found in your CSV file.');
    }

    return $importedCount;
}


}
