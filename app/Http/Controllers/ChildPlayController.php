<?php

namespace App\Http\Controllers;

use App\Models\ChildProfile;
use App\Models\LearningActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChildPlayController extends Controller
{
    public function show(Request $request, ChildProfile $child): View
    {
        abort_unless($child->parent_id === $request->user()->id, 403);

        $games = $child->allowedGames()
        ->where('games.is_visible', true)
        ->get();
        
        $activities = $request->user()->learningActivities()
            ->where('is_active', true)
            ->latest()
            ->get();

        $completedIds = $child->progressRecords()
            ->where('status', 'completed')
            ->pluck('learning_activity_id')
            ->all();

        $worksheetAssignments = $child->worksheetAssignments()
            ->with('worksheet')
            ->latest()
            ->get();

        return view('children.play', [
            'child' => $child,
            'activities' => $activities,
            'completedIds' => $completedIds,
            'worksheetAssignments' => $worksheetAssignments,
        ]);
    }

    public function complete(Request $request, ChildProfile $child, LearningActivity $activity): RedirectResponse
    {
        abort_unless($child->parent_id === $request->user()->id, 403);
        abort_unless($activity->parent_id === $request->user()->id, 403);

        $child->progressRecords()->updateOrCreate(
            ['learning_activity_id' => $activity->id],
            ['status' => 'completed', 'completed_at' => now()]
        );

        return back()->with('status', 'Progress saved.');
    }
}
