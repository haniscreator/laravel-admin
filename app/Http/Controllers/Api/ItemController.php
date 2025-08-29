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
        $orderBy = $request->input('order_by', 'created_at'); // default
        $orderDir = $request->input('order_dir', 'desc'); // default
        $searchTerm = $request->input('search_term');

        $query = Item::with('album')
            ->where('status', 1); // ✅ only active items

        // Filter by album
        if ($albumId) {
            $query->where('album_id', $albumId);
        }

        // Search filter
        if (! empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%")
                    ->orWhere('keyword', 'like', "%{$searchTerm}%")
                    ->orWhereHas('album', function ($albumQuery) use ($searchTerm) {
                        $albumQuery->where('name', 'like', "%{$searchTerm}%");
                    });
            });
        }

        // Whitelist ordering fields
        $allowedOrderFields = ['id', 'name', 'created_at', 'status', 'created_by'];
        if (! in_array($orderBy, $allowedOrderFields)) {
            $orderBy = 'created_at';
        }

        $orderDir = strtolower($orderDir) === 'asc' ? 'asc' : 'desc';

        $query->orderBy($orderBy, $orderDir);

        $items = $query->paginate($perPage);

        return ItemResource::collection($items)
            ->response()
            ->setStatusCode(200);
    }
}
