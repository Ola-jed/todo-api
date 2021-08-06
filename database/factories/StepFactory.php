<?php

namespace Database\Factories;

use App\Models\Step;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

class StepFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Step::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'title'       => $this->faker->sentence,
            'priority'    => $this->faker->numberBetween(1, 10),
            'is_finished' => $this->faker->boolean(),
            'task_id'     => $this->faker->randomElement(Task::whereHasSteps(true)->pluck('id')->toArray())
        ];
    }
}
