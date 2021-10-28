<?php

namespace Database\Factories;

use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Question::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "question" => $this->faker->text(50),
            "weight" => $this->faker->numberBetween(0, 100),
            "question" => $this->faker->text(50),
            "answer" => $this->faker->text(20),
        ];
    }
}
