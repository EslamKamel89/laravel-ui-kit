<?php

namespace Database\Seeders;

use App\Models\Meeting;
use App\Models\Tag;
use App\Models\User;
use App\Models\UserSetting;
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

        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
        ]);
        $this->command->info('admin user created!');
        Meeting::factory()->create([
            'title' => 'Spring Planning',
            'date' => now()->addDay(),
            'notes' => <<<'TEXT'
            - Review last spring performance
            - Set goals for upcoming sprints
            - Assign task to team members
            TEXT
        ]);
        $this->command->info('Meeting created!');
        UserSetting::create([
            'name' => 'John Doe',
            'theme' => 'light',
            'language' => 'english',
        ]);

        $this->command->info('User settings created!');
        Tag::factory(30)->create();
        $this->command->info('Tags created!');
        $this->call(PostSeeder::class);
        $this->command->info('Posts seeded successfully');
    }
}
