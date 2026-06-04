<?php

namespace App\Http\Controllers;

use App\Models\LearningActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LearningActivityController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'domain' => ['required', 'in:'.implode(',', LearningActivity::DOMAINS)],
            'prompt' => ['required', 'string', 'max:500'],
            'audio_prompt' => ['nullable', 'string', 'max:255'],
            'age_min' => ['required', 'integer', 'min:2', 'max:8'],
            'age_max' => ['required', 'integer', 'min:2', 'max:8', 'gte:age_min'],
            'button_color' => ['required', 'string', 'max:16'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $request->user()->learningActivities()->create([
            ...$validated,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('status', 'Learning activity added.');
    }

    public function destroy(Request $request, LearningActivity $activity): RedirectResponse
    {
        abort_unless($activity->parent_id === $request->user()->id, 403);

        $activity->delete();

        return back()->with('status', 'Learning activity removed.');
    }
}
