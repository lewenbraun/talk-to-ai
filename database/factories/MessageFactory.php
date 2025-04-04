<?php

namespace Database\Factories;

use App\Enums\RoleEnum;
use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends Factory<Message>
 */
class MessageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Message::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'chat_id' => $this->faker->randomNumber(),
            'content' => $this->faker->paragraph(),
            'role' => Arr::random(array_column(RoleEnum::cases(), 'value')),
        ];
    }
}
