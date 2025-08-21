<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AlbumResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'keyword' => $this->keyword,
            'location' => $this->location,
            'status' => $this->status,
            'created_date' => $this->created_date,
            'created_by' => $this->created_by,
            'item_count' => $this->items()->count(),
        ];
    }
}
