<?php

namespace Database\Seeders\SeederClasses;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate([
            'email' => 'super@root.com',
        ], [
            'name' => 'Super Root',
            'password' => bcrypt(\Str::random()),
        ]);
    }
}
