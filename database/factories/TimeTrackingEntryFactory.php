<?php

namespace Database\Factories;

use App\Models\TimeTrackingEntry;
use App\Models\User;
use App\Models\WorkType;
use App\Models\BreakReason;
use Illuminate\Database\Eloquent\Factories\Factory;

class TimeTrackingEntryFactory extends Factory
{
    protected $model = TimeTrackingEntry::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'type' => $this->faker->randomElement(['work', 'break']),
            'start_time' => $this->faker->dateTimeBetween('-1 week'),
            'end_time' => $this->faker->optional()->dateTimeBetween('now', '+1 week'),
            'work_type_id' => WorkType::factory(),
            'break_reason_id' => BreakReason::factory(),
        ];
    }
}