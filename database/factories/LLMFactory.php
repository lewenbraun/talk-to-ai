<?php

namespace Database\Factories;

use App\Models\Llm;
use App\Models\AiService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Llm>
 */
class LLMFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LLM::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => $this->faker->randomNumber(),
            'ai_service_id' => AiService::first()->id,
            'name' => $this->faker->word(),
            'isLoaded' => $this->faker->boolean(),
        ];
    }
}
