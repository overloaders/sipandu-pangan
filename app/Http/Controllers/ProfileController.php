<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Web\UpdateProfileRequest;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user()->load('unit', 'roles');
        return view('profile', compact('user'));
    }

    public function update(UpdateProfileRequest $request)
    {
        $data = $request->validated();
        auth()->user()->update($data);
        return back()->with('success', 'Profil berhasil diperbarui');
    }
}
