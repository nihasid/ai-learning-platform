<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\ChildProfile;
use App\Models\ChildGamePermission;


class ParentGameController extends Controller
{
    //
    public function index()
    {
        $children = auth()->user()->children;
        $games = Game::where('is_visible', true)->get();

        return view('games.index', compact('children', 'games'));
    }
}
