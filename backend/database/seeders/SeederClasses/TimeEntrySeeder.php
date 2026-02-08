<?php

namespace Database\Seeders\SeederClasses;

use App\Models\TimeEntry;
use Illuminate\Database\Seeder;

class TimeEntrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TimeEntry::factory(200)->create();
    }
}
