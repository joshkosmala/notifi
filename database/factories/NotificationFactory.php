<?php

namespace Database\Factories;

use App\Models\Organisation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organisation_id' => Organisation::factory(),
            'title' => fake()->text(50),
            'body' => fake()->text(280),
            'link' => fake()->optional()->url(),
        ];
    }

    /**
     * Indicate that the notification has been sent.
     */
    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'sent_at' => now(),
        ]);
    }

    /**
     * Indicate that the notification is scheduled for future delivery.
     */
    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'scheduled_for' => now()->addHours(fake()->numberBetween(1, 48)),
        ]);
    }
}
