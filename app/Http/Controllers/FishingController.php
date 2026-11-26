<?php

namespace App\Http\Controllers;

use App\Models\FishPond;
use Illuminate\Http\Request;

class FishingController extends Controller
{
    public function index(Request $request)
    {
        return redirect()->route('fish-ponds.index');
    }
}
