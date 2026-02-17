<?php

namespace Modules\Todo\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Todo\Models\Todo;
use Modules\User\Models\User;

class TodoDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        $todos = [
            ['title' => 'Buy groceries', 'description' => 'Milk, eggs, bread', 'completed' => false],
            ['title' => 'Read a book', 'description' => 'Finish the current chapter', 'completed' => true],
            ['title' => 'Go for a run', 'description' => null, 'completed' => false],
            ['title' => 'Clean the house', 'description' => 'Vacuum and mop', 'completed' => true],
            ['title' => 'Call mom', 'description' => null, 'completed' => false],
        ];

        foreach ($users as $user) {
            foreach ($todos as $todo) {
                Todo::firstOrCreate(
                    ['user_id' => $user->id, 'title' => $todo['title']],
                    [...$todo, 'user_id' => $user->id]
                );
            }
        }
    }
}
