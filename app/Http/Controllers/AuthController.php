<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class AuthController extends Controller
{
    public function register(Request $request, ResultController $resultController)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'image' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $existingUser = User::where('first_name', $validated['first_name'])
            ->where('name', $validated['name'])
            ->first();

        if ($existingUser) {
            return redirect()->back()->withErrors([
                'name' => __('validation.test_name', [
                    'first_name' => $existingUser->first_name,
                    'name' => $existingUser->name
                ]),
            ]);
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('profiles', 'public');
        } else {
            $uniqueName = 'profiles/' . Str::random(40) . '.png';

            if (!Storage::disk('public')->exists($uniqueName)) {
                Storage::disk('public')->copy('default_profile.png', $uniqueName);
            }

            $validated['image'] = $uniqueName;
        }

        $user = User::create($validated);

        Auth::login($user);

        Cache::put("upload_progress_" . auth()->id(), 0);
        Cache::put("upload_total_" . auth()->id(), 0);

        return redirect()->route('home');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return redirect()->back()->withErrors([
                'email' => 'Impossible de trouver un compte correspondant à cette adresse e-mail',
            ]);
        }

        if (!Auth::attempt($validated)) {
            return redirect()->back()->withErrors([
                'password' => 'Le mot de passe est incorrect.',
            ]);
        }

        $request->session()->regenerate();

        Cache::put("upload_progress_" . auth()->id(), 0);
        Cache::put("upload_total_" . auth()->id(), 0);

        return redirect()->route('home');
    }

    public function logout(Request $request)
    {
        $locale = Session::get(key: 'locale') ?? 'fr';

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Session::put('locale', $locale);

        return redirect()->route('home');
    }
}
