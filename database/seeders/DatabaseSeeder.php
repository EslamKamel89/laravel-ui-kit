<?php

namespace Database\Seeders;

use App\Models\Meeting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
        ]);
        Meeting::factory()->create([
            'title' => 'Spring Planning',
            'date' => now()->addDay(),
            'notes' => <<<'TEXT'
            - Review last spring performance
            - Set goals for upcoming sprints
            - Assign task to team members
            TEXT
        ]);
    }
}
