<?php

namespace Database\Seeders\SeederClasses;

use App\Models\TimeFrame;
use Illuminate\Database\Seeder;

class TimeframeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TimeFrame::factory(30)->create();
    }
}
