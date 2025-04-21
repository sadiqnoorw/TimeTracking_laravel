<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BreakReason;

class BreakReasonsTableSeeder extends Seeder
{
    public function run()
    {
        $reasons = [
            'Lunch Break',
            'Coffee Break',
            'Personal Errand',
            'Meeting Pause',
            'Other'
        ];

        foreach ($reasons as $reason) {
            BreakReason::create(['reason' => $reason]);
        }
    }
}
