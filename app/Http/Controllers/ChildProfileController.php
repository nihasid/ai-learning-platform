<?php

namespace App\Http\Controllers;

use App\Models\ChildProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ChildProfileController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'birthdate' => ['nullable', 'date', 'before:today'],
            'avatar_color' => ['required', 'string', 'max:16'],
            'audio_guidance_enabled' => ['nullable', 'boolean'],
        ]);

        $request->user()->childProfiles()->create([
            ...$validated,
            'audio_guidance_enabled' => $request->boolean('audio_guidance_enabled', true),
        ]);

        return back()->with('status', 'Child profile added.');
    }

    public function destroy(Request $request, ChildProfile $child): RedirectResponse
    {
        abort_unless($child->parent_id === $request->user()->id, 403);

        $child->delete();

        return back()->with('status', 'Child profile removed.');
    }
}
