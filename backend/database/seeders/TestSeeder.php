<?php

namespace Database\Seeders;

use Database\Seeders\SeederClasses\PreferenceSeeder;
use Database\Seeders\SeederClasses\ProjectSeeder;
use Database\Seeders\SeederClasses\TimeEntrySeeder;
use Database\Seeders\SeederClasses\TimeframeSeeder;
use Database\Seeders\SeederClasses\UserSeeder;
use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            PreferenceSeeder::class,
            ProjectSeeder::class,
            TimeframeSeeder::class,
            TimeEntrySeeder::class,
        ]);
    }
}
