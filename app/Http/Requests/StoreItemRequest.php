<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorize logic (adjust if you use policies)
        return true;
    }

    public function rules(): array
    {
        return [
            'album_id' => 'required|exists:albums,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'keyword' => 'required|string',
            'media_url' => 'nullable|file|mimes:mp3,wav,ogg',
            'status' => 'required|boolean',
        ];
    }
}
