<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Dashboard', [
            'albums' => \App\Models\Album::count(),
            'items' => \App\Models\Item::count(),
        ]);
    }
}
