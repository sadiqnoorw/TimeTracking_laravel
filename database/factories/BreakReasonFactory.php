<?php
namespace Database\Factories;

use App\Models\BreakReason;
use Illuminate\Database\Eloquent\Factories\Factory;

class BreakReasonFactory extends Factory
{
    protected $model = BreakReason::class;

    public function definition()
    {
        return [
            'reason' => $this->faker->word,
        ];
    }
}
