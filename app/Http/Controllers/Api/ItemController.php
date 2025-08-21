<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ItemResource;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $albumId = $request->input('album_id');

        $query = Item::with('album');

        if ($albumId) {
            $query->where('album_id', $albumId);
        }

        $items = $query->paginate($perPage);

        return ItemResource::collection($items)
            ->response()
            ->setStatusCode(200);
    }
}
