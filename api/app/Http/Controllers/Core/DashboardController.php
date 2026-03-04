<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Posts;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['auth']);
    }

    public function index(Request $request)
    {
        return Inertia::render('Home/Dashboard', [
        ]);
    }

}
