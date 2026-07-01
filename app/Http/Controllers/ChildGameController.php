<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChildProfile;

class ChildGameController extends Controller
{
    //
    public function findSearch(ChildProfile $child)
    {
        abort_unless($child->user_id === auth()->id(), 403);

        abort_unless(
            $child->allowedGames()
                ->where('games.slug', 'find-and-search')
                ->where('games.is_visible', true)
                ->exists(),
            403
        );
            return view('games.find-and-search', compact('child'));
    }
}
