<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkType;

class WorkTypesTableSeeder extends Seeder
{
    public function run()
    {
        $types = [
            'Project Work',
            'Travel Time',
            'Meeting',
            'Support'
        ];

        foreach ($types as $type) {
            WorkType::create(['name' => $type]);
        }
    }
}
