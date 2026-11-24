<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'street_address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'province' => ['required', 'string', 'max:100'],
            'country_code' => ['required', 'string', 'max:5'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $user = $request->user();

        $user->update([
            ...$validated,
            'name' => trim($validated['first_name'].' '.$validated['last_name']),
            'profile_completed_at' => $user->profile_completed_at ?? now(),
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Your profile is ready.');
    }
}
