<?php

namespace Database\Factories;

use App\Models\AiService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AiService>
 */
class AiServiceFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = AiService::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
        ];
    }
}
