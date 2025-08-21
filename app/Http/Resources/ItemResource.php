<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
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
            'album' => $this->album ? $this->album->name : null,
            'name' => $this->name,
            'description' => $this->description,
            'keyword' => $this->keyword,
            'media_url' => $this->media_url,
            'status' => $this->status,
            'created_by' => $this->created_by,
        ];
    }
}
