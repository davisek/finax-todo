<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

//        $this->call(CategoryDatabaseSeeder::class);

        DB::commit();
    }
}
