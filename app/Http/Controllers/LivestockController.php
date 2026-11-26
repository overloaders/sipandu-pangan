<?php

namespace App\Http\Controllers;

use App\Models\LivestockBarn;
use Illuminate\Http\Request;

class LivestockController extends Controller
{
    public function index(Request $request)
    {
        return redirect()->route('livestock-barns.index');
    }
}
