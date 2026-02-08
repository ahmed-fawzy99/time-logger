<?php

namespace Database\Seeders;

use Database\Seeders\SeederClasses\PreferenceSeeder;
use Database\Seeders\SeederClasses\UserSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            PreferenceSeeder::class,
        ]);
    }
}
