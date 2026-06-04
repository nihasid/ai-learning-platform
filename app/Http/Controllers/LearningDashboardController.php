<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class LearningDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $parent = $request->user();
        $children = $parent->childProfiles()
            ->with(['progressRecords.learningActivity'])
            ->latest()
            ->get();

        $activities = $parent->learningActivities()
            ->withCount(['progressRecords as completions_count' => fn ($query) => $query->where('status', 'completed')])
            ->latest()
            ->get();

        return view('dashboard', [
            'children' => $children,
            'activities' => $activities,
        ]);
    }
}
