<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Game;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Game::updateOrCreate(
            ['slug' => 'find-and-search'],
            [
                'title' => 'Find and Search',
                'age'   => '3-5',
                'description' => 'Find hidden letters around the room.',
                'category' => 'Find and Search',
                'route_name' => 'children.games.find-search',
                'thumbnail_path' => 'kidkinder/img/game1.jpg',
                'is_visible' => true,
            ]
        );
    }
}
