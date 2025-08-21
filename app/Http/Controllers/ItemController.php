<?php

namespace App\Http\Controllers;

use App\Actions\Item\DeleteItemAction;
use App\Actions\Item\GetItemForEditAction;
use App\Actions\Item\IndexItemsAction;
use App\Actions\Item\ItemImportAction;
use App\Actions\Item\StoreItemAction;
use App\Actions\Item\ToggleItemStatusAction;
use App\Actions\Item\UpdateItemAction;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Item;
use App\Services\AlbumService;
use App\Services\ItemService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ItemController extends Controller
{
    protected $itemService;

    protected $albumService;

    public function __construct(ItemService $itemService, AlbumService $albumService)
    {
        $this->itemService = $itemService;
        $this->albumService = $albumService;
    }

    public function index(Request $request, IndexItemsAction $action)
    {
        $result = $action->handle($request);

        return Inertia::render('Items/Index', $result);
    }

    public function create()
    {
        $albums = $this->albumService->getActiveForDropdown();

        return Inertia::render('Items/Create', [
            'albums' => $albums,
        ]);
    }

    public function store(StoreItemRequest $request, StoreItemAction $action)
    {
        $action->handle($request);

        return redirect()->route('items.index', [
            'sort' => 'id',
            'direction' => 'desc',
            'page' => 1,
        ])->with('success', 'Item created successfully.');
    }

    public function edit(Item $item, GetItemForEditAction $action)
    {
        $data = $action->handle($item);
        $albums = $this->albumService->getActiveForDropdown();

        return Inertia::render('Items/Edit', [
            'item' => $data['item'],
            'albums' => $albums,
        ]);

    }

    public function update(UpdateItemRequest $request, Item $item, UpdateItemAction $action)
    {
        $action->handle($request, $item);

        return redirect()->route('items.index', [
            'sort' => 'updated_at',
            'direction' => 'desc',
            'page' => 1,
        ])->with('success', 'Item updated successfully.');
    }

    public function destroy(Item $item, DeleteItemAction $action)
    {
        $action->handle($item);

        return redirect()->route('items.index')->with('success', 'Item deleted successfully.');
    }

    public function toggleStatus(Request $request, Item $item, ToggleItemStatusAction $action)
    {
        $action->handle($item);

        return redirect()->back()->with('success', 'Item status updated.');
    }

    public function import(Request $request, ItemImportAction $action)
    {
        try {
            $count = $action->handle($request);

            return response()->json([
                'success' => true,
                'message' => "{$count} items imported successfully.",
                'count' => $count,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Import failed@.',
            ], 422);
        }
    }
}
