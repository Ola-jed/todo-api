<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $title = $this->faker->catchPhrase;
        $hasSteps = $this->faker->boolean();
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'has_steps' => $hasSteps,
            'description' => $this->faker->realText(),
            'date_limit' => Carbon::now()->addDays($this->faker->numberBetween(1,50))->toDate(),
            'is_finished' => $this->faker->boolean(),
            'priority' => $this->faker->numberBetween(1,10),
            'user_id' => $this->faker->randomElement(User::all()->pluck('id')->all())
        ];
    }
}
