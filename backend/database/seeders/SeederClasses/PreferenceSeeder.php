<?php

namespace Database\Seeders\SeederClasses;

use App\Models\Preference;
use App\Models\User;
use Illuminate\Database\Seeder;

class PreferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::whereDoesntHave('preferences')->get();
        foreach ($users as $user) {
            Preference::factory()->create([
                'user_id' => $user->id,
                'additional_properties' => [
                    //                    'roundDurationTo' => 0,
                    //                    'roundMethod' => 'nearest',
                    'invoiceName' => 'Super Root',
                    'invoiceTitle' => 'Kittens United Inc.',
                    'invoiceAddress' => '987 Pyramids St, Cairo, EG',
                ],
            ]);
        }
    }
}
