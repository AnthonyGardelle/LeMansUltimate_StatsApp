<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function update(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
            'image' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user = Auth::user();

        if ($request->hasFile('image')) {
            if ($user->image && $user->image !== 'images/profile.png') {
                Storage::disk('public')->delete($user->image);
            }

            $imagePath = $request->file('image')->store('profiles', 'public');
            $validated['image'] = $imagePath;
        }

        $user->update([
            'first_name' => $validated['first_name'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'image' => $validated['image'] ?? $user->image,
        ]);

        return back()->with('success', 'Profil mis à jour avec succès.');
    }
}
