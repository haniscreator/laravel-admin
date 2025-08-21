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
        $perPage = $request->query('per_page', 10);

        // Eager load album to avoid N+1 problem
        $items = Item::with('album')->paginate($perPage);

        return ItemResource::collection($items)
            ->response()
            ->setStatusCode(200);
    }
}
