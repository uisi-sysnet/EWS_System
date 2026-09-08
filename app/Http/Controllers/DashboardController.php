<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Signal;

class DashboardController extends Controller
{
    public function index()
    {
        $signals = Signal::all();
        return view('controller', compact('signals'));
    }
}
