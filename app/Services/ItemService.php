<?php

namespace App\Services;

use App\Models\Image;
use App\Models\Item;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ItemService
{
    protected $item;

    public function __construct(Item $item)
    {
        $this->item = $item;
    }

    /**
     * Search/sort/paginate item query.
     */
    public function getList($request, $perPage = 10)
    {
        $query = $this->item->with('album');

        if ($request->filled('album_id')) {
            $query->where('album_id', $request->album_id);
        }

        if ($request->filled('keyword')) {
            $kw = $request->keyword;
            $query->where(function ($q) use ($kw) {
                $q->where('name', 'like', '%'.$kw.'%')
                    ->orWhere('description', 'like', '%'.$kw.'%')
                    ->orWhere('keyword', 'like', '%'.$kw.'%');
            });
        }

        if ($request->filled('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Default sort field and direction
        $allowedSortFields = ['id', 'name', 'description'];
        $sortField = $request->get('sort', 'id');        // default id
        $sortDirection = $request->get('direction', 'desc'); // default desc

        if (! in_array($sortField, $allowedSortFields)) {
            $sortField = 'id';
        }

        if (! in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        $query->orderBy($sortField, $sortDirection);

        return $query->paginate($perPage)
            ->through(fn ($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'description' => $item->description,
                'keyword' => $item->keyword,
                'status' => $item->status,
                'album_name' => optional($item->album)->name,
            ])
            ->appends($request->only('keyword', 'album_id', 'status', 'sort', 'direction'));
    }

    /**
     * Create item with optional image.
     */
    public function store(array $data, ?UploadedFile $mediaFile = null): Item
    {
        $data = $this->prepareItemData($data);

        if ($mediaFile) {
            $mediaPath = $mediaFile->storeAs(
                'media',
                time().'.'.$mediaFile->getClientOriginalExtension(),
                'public'
            );
            $data['media_url'] = $mediaPath;
        } else {
            throw new \Exception('Media file is missing.');
        }

        return $this->item->create($data);
    }

    /**
     * Update item and optionally replace image.
     */
    public function update(Item $item, array $data, ?UploadedFile $image = null, ?int $userId = null)
    {
        $data = $this->prepareItemData($data);
        $item->update($data);

        if ($image) {
            $this->storeImage($item->id, $image, $userId);
        }

        return $item;
    }

    public function delete(Item $item)
    {
        $this->deleteItemImages($item);
        $item->delete();
    }

    public function toggleStatus(Item $item): Item
    {
        $item->status = ! $item->status;
        $item->save();

        return $item;
    }

    public function getItemForEdit(Item $item): array
    {
        $image = Image::where('parent_id', $item->id)->where('type', 'item')->first();

        return [
            'item' => $item,
            'image' => $image,

        ];
    }

    /**
     * Store / replace image for item.
     */
    public function storeImage(int $parentId, UploadedFile $image, ?int $userId, string $type = 'item')
    {
        // Delete existing
        $existing = Image::where('parent_id', $parentId)
            ->where('type', $type)
            ->get();

        foreach ($existing as $row) {
            if (Storage::disk('public')->exists($row->path)) {
                Storage::disk('public')->delete($row->path);
            }
            $row->delete();
        }

        // Store new
        $filename = time().'.'.$image->getClientOriginalExtension();
        $path = $image->storeAs('images/upload', $filename, 'public');

        return Image::create([
            'parent_id' => $parentId,
            'path' => $path,
            'type' => $type,
            'created_by' => $userId,
        ]);
    }

    public function deleteItemImages(Item $item, string $type = 'item')
    {
        $existing = Image::where('parent_id', $item->id)
            ->where('type', $type)
            ->get();

        foreach ($existing as $row) {
            if (Storage::disk('public')->exists($row->path)) {
                Storage::disk('public')->delete($row->path);
            }
        }

        Image::where('parent_id', $item->id)
            ->where('type', $type)
            ->delete();
    }

    /**
     * CSV Import (similar to AlbumService).
     */
    public function importCsv(UploadedFile $file, int $userId): int
    {
        $handle = $this->openCsvFile($file);

        try {
            $header = $this->validateHeader($handle);

            $importedCount = 0;

            while (($row = fgetcsv($handle)) !== false) {
                if (! $this->isValidRow($row, $header)) {
                    continue;
                }

                $data = $this->mapRowToData($row, $header);

                if (! $this->validateRowData($data)) {
                    continue;
                }

                $data = $this->prepareItemData($data);

                Item::create([
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'keyword' => $data['keyword'],
                    'location' => $data['location'],
                    'status' => $data['status'],
                    'created_date' => now(),
                    'created_by' => $userId,
                    'album_id' => $data['album_id'] ?? null,
                ]);

                $importedCount++;
            }

            if ($importedCount === 0) {
                throw new \Exception('No valid items were found in your CSV file.');
            }

            return $importedCount;

        } finally {
            fclose($handle);
        }
    }

    /** ------------------ Helpers ------------------ */
    protected function normalizeKeywords(string $raw): string
    {
        $parts = preg_split('/[\s,]+/', $raw, -1, PREG_SPLIT_NO_EMPTY);
        $tags = array_unique(array_map('trim', $parts));

        return implode(', ', $tags);
    }

    protected function prepareItemData(array $data): array
    {
        if (! empty($data['keyword'])) {
            $data['keyword'] = $this->normalizeKeywords($data['keyword']);
        }

        return $data;
    }

    protected function openCsvFile(UploadedFile $file)
    {
        $handle = fopen($file->getRealPath(), 'r');
        if ($handle === false) {
            throw new \Exception('Unable to open the uploaded CSV file.');
        }

        return $handle;
    }

    protected function validateHeader($handle): array
    {
        $header = fgetcsv($handle);
        if (! $header) {
            throw new \Exception('CSV file is empty or unreadable.');
        }

        $header = array_map('trim', $header);
        $expected = ['name', 'description', 'keyword', 'location', 'status', 'album_id'];

        if ($header !== $expected) {
            throw new \Exception(
                'Your CSV header is incorrect. Expected columns: '.implode(', ', $expected)
            );
        }

        return $header;
    }

    protected function isValidRow(array $row, array $header): bool
    {
        return count($row) >= count($header);
    }

    protected function mapRowToData(array $row, array $header): array
    {
        return array_combine($header, array_map('trim', $row));
    }

    protected function validateRowData(array $data): bool
    {
        return isset($data['name'], $data['status']) &&
            $data['name'] !== '' &&
            $data['status'] !== '';
    }
}
