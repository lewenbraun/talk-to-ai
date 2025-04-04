<?php

namespace Database\Factories;

use App\Models\AiService;
use App\Models\User;
use App\Models\UserSettingAiService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserSettingAiService>
 */
class UserSettingAiServiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserSettingAiService::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => $this->faker->randomNumber(),
            'ai_service_id' => $this->faker->randomNumber(),
            'api_key' => $this->faker->optional()->uuid(),
            'url_api' => $this->faker->optional()->url(),
        ];
    }
}
