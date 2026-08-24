<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tasks = [
            ['title' => 'Finalize guest speaker list', 'status' => 'todo', 'position' => 0],
            ['title' => 'Order table centerpieces', 'status' => 'todo', 'position' => 1],
            ['title' => 'Arrange parking signage', 'status' => 'todo', 'position' => 2],
            ['title' => 'Confirm catering menu with vendor', 'status' => 'in-progress', 'position' => 0],
            ['title' => 'Design event program booklet', 'status' => 'in-progress', 'position' => 1],
            ['title' => 'Send invitations to attendees', 'status' => 'in-progress', 'position' => 2],
            ['title' => 'Book venue and sign contract', 'status' => 'done', 'position' => 0],
            ['title' => 'Hire photographer for the day', 'status' => 'done', 'position' => 1],
        ];

        foreach ($tasks as $task) {
            Task::create($task);
        }
    }
}
