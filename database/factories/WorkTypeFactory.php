<?php

namespace Database\Factories;

use App\Models\WorkType;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkTypeFactory extends Factory
{
    protected $model = WorkType::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
        ];
    }
}
