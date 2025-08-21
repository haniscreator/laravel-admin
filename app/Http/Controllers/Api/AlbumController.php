<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AlbumResource;
use App\Models\Album;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
    public function index(Request $request)
    {
        // Pagination: 10 per page by default, can be changed via query param
        $perPage = $request->query('per_page', 10);

        $albums = Album::withCount('items')->paginate($perPage);

        // Return paginated response using resource collection
        return AlbumResource::collection($albums)
            ->response()
            ->setStatusCode(200);
    }
}
