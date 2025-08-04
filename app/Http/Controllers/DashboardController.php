<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Item;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Dashboard', [
            'albums' => 0,
            'items' => 0,
        ]);
    }
}
