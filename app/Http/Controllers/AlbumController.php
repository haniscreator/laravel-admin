<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Actions\Album\IndexAlbumsAction;
use App\Actions\Album\StoreAlbumAction;
use App\Actions\Album\GetAlbumForEditAction;
use App\Actions\Album\UpdateAlbumAction;
use App\Actions\Album\DeleteAlbumAction;
use App\Actions\Album\ToggleAlbumStatusAction;
use App\Http\Requests\StoreAlbumRequest;
use App\Http\Requests\UpdateAlbumRequest;
use App\Models\Album;
use App\Actions\Album\AlbumImportAction;
use App\Actions\Album\ImportAlbumsFromCsv;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AlbumController extends Controller
{
    public function index(Request $request, IndexAlbumsAction $action)
    {
        $result = $action->handle($request);

        return Inertia::render('Albums/Index', $result);
    }

    public function create()
    {
        return Inertia::render('Albums/Create');
    }

    public function store(StoreAlbumRequest $request, StoreAlbumAction $action)
    {
        $action->handle($request);

        return redirect()->route('albums.index')->with('success', 'Album created successfully.');
    }

    public function edit(Album $album, GetAlbumForEditAction $action)
    {
        $data = $action->handle($album);

        return Inertia::render('Albums/Edit', $data);
    }

    public function update(UpdateAlbumRequest $request, Album $album, UpdateAlbumAction $action)
    {
        $action->handle($request, $album);

        return redirect()->route('albums.index')->with('success', 'Album updated successfully.');
    }

    public function destroy(Album $album, DeleteAlbumAction $action)
    {
        $action->handle($album);

        return redirect()->route('albums.index')->with('success', 'Album deleted successfully.');
    }

    public function toggleStatus(Request $request, Album $album, ToggleAlbumStatusAction $action)
    {
        $action->handle($album);

        // Because your UI expects an Inertia response sometimes, returning back is safe:
        return redirect()->back()->with('success', 'Status updated.');
    }
    
public function import(Request $request, AlbumImportAction $action)
{
    try {
        $count = $action->handle($request);

        return response()->json([
            'success' => true,
            'message' => "{$count} albums imported successfully.",
            'count'   => $count,
        ], 200);
    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage() ?: 'Import failed.',
        ], 422);
    }
}




    /*
   public function import(Request $request, AlbumImportAction $action)
    {
        try {
            $result = $action->handle($request);

            if ($result['success']) {
                return redirect()->back()
                    ->with('success', "{$result['count']} albums imported successfully.");
            } else {
                return redirect()->back()
                    ->with('error', 'Import failed.');
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
        */

    /*
    public function import(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'csv' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->with('error', 'Invalid file. Please upload a valid CSV.');
        }

        $file = $request->file('csv');
        $importedCount = 0;

        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            $header = fgetcsv($handle);
            $header = array_map('trim', $header);

            $expected = ['name', 'description', 'keyword', 'location', 'status'];

            if ($header !== $expected) {
                return back()->with('error', 'Invalid CSV header. Expected: ' . implode(', ', $expected));
            }

            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < count($expected)) {
                    continue;
                }

                $data = array_combine($expected, $row);

                Album::create([
                    'name'         => $data['name'],
                    'description'  => $data['description'],
                    'keyword'      => $data['keyword'],
                    'location'     => $data['location'],
                    'status'       => $data['status'],
                    'created_date' => now(),
                    'created_by'   => Auth::id(),
                ]);

                $importedCount++;
            }

            fclose($handle);
        }

        return back()->with('success', "{$importedCount} albums imported successfully.");
    }*/

}
