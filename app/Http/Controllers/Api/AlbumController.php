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
        $perPage = $request->input('per_page', 7);              // ✅ default 7
        $orderBy = $request->input('order_by', 'created_at');   // ✅ default
        $orderDir = $request->input('order_dir', 'desc');        // ✅ default
        $searchTerm = $request->input('search_term');

        $query = Album::withCount('items')
            ->with('coverImage')
            ->where('status', 1); // ✅ only active albums

        // Search filter
        if (! empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%")
                    ->orWhere('keyword', 'like', "%{$searchTerm}%")
                    ->orWhere('location', 'like', "%{$searchTerm}%");
            });
        }

        // Whitelist ordering fields
        $allowedOrderFields = ['id', 'name', 'created_at', 'status', 'created_by', 'location'];
        if (! in_array($orderBy, $allowedOrderFields)) {
            $orderBy = 'created_at';
        }

        $orderDir = strtolower($orderDir) === 'asc' ? 'asc' : 'desc';

        $query->orderBy($orderBy, $orderDir);

        // \Log::info($query->toSql());
        // \Log::info($query->getBindings());

        $albums = $query->paginate($perPage);

        return AlbumResource::collection($albums)
            ->response()
            ->setStatusCode(200);
    }
}
