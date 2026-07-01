<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserPreference;
use App\Models\Category;
use App\Models\ChildProfile;
use App\Models\LearningActivity;
use App\Models\Permission;
use App\Models\Task;
use App\Models\Worksheet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $permissions = collect([
            [Permission::MANAGE_USERS, 'Manage accounts'],
            [Permission::MANAGE_WORKSHEETS, 'Manage worksheet library'],
            [Permission::VIEW_REPORTS, 'View reports'],
        ])->map(fn ($permission) => Permission::firstOrCreate(
            ['name' => $permission[0]],
            ['label' => $permission[1]]
        ));

        $admin = User::factory()->create([
            'name' => 'Worksheet Admin',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        $admin->permissions()->sync($permissions->pluck('id'));

        $user = User::factory()->create([
            'name'  => 'Demo Parent',
            'email' => 'parent@example.com',
            'role' => 'parent',
        ]);

        $user->permissions()->sync(
            $permissions->where('name', Permission::VIEW_REPORTS)->pluck('id')
        );
    
        UserPreference::create([
            'user_id'             => $user->id,
            'peak_hours'          => [9, 10, 11, 14, 15, 16],
            'focus_block_minutes' => 90,
            'break_minutes'       => 15,
        ]);
    
        $categories = collect(['Work', 'Health', 'Personal', 'Learning'])
            ->map(fn ($name) => Category::create([
                'user_id' => $user->id,
                'name'    => $name,
                'color'   => collect(['#6366f1','#10b981','#f59e0b','#ef4444'])->random(),
            ]));
    
        Task::factory()->count(8)->create([
            'user_id'     => $user->id,
            'category_id' => $categories->random()->id,
            'title'       => 'Finish project report',
            'status'      => 'pending',
        ]);

        $child = ChildProfile::create([
            'parent_id' => $user->id,
            'name' => 'Mia',
            'birthdate' => now()->subYears(4)->toDateString(),
            'avatar_color' => '#22c55e',
            'audio_guidance_enabled' => true,
        ]);

        collect([
            ['Tap Letter A', 'literacy', 'Find the letter A and tap it.', '#ef4444'],
            ['Count Three Stars', 'numeracy', 'Count the stars with me: one, two, three.', '#3b82f6'],
            ['Big Stretch', 'motor', 'Reach up high, then tap the button.', '#f97316'],
            ['Feelings Check', 'social_emotional', 'Show me a happy face.', '#a855f7'],
        ])->each(function ($activity) use ($user, $child) {
            $lesson = LearningActivity::create([
                'parent_id' => $user->id,
                'title' => $activity[0],
                'domain' => $activity[1],
                'prompt' => $activity[2],
                'audio_prompt' => $activity[2],
                'age_min' => 2,
                'age_max' => 8,
                'button_color' => $activity[3],
                'is_active' => true,
            ]);

            if ($activity[1] === 'literacy') {
                $child->progressRecords()->create([
                    'learning_activity_id' => $lesson->id,
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);
            }
        });

        $demoPdf = "%PDF-1.4\n1 0 obj\n<<>>\nendobj\ntrailer\n<<>>\n%%EOF\n";
        Storage::disk('public')->put('worksheets/demo-counting.pdf', $demoPdf);
        Storage::disk('public')->put('worksheets/demo-drawing.pdf', $demoPdf);

        collect([
            ['Count Five Dots', 'math', '4-5', 'demo-counting.pdf', 'Practice counting from one to five.'],
            ['Draw Big Circles', 'drawing', '4-5', 'demo-drawing.pdf', 'Practice early pencil control with circles.'],
        ])->each(function ($worksheet) use ($admin,$user, $child) {
            $uploaded = Worksheet::create([
                'uploaded_by' => $admin->id,
                'title' => $worksheet[0],
                'subject' => $worksheet[1],
                'age_group' => $worksheet[2],
                'description' => $worksheet[4],
                'file_path' => 'worksheets/'.$worksheet[3],
                'original_filename' => $worksheet[3],
                'mime_type' => 'application/pdf',
            ]);

            $child->worksheetAssignments()->create([
                'worksheet_id' => $uploaded->id,
                'status' => 'assigned',
                'assigned_at' => now(),
            ]);
        });

        $this->call(GameSeeder::class);
    }
    
}
