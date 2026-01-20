<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscriber>
 */
class SubscriberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate NZ mobile numbers (021, 022, 027, 029)
        $prefix = fake()->randomElement(['21', '22', '27', '29']);
        $number = fake()->numerify('#######');

        return [
            'phone' => '+64'.$prefix.$number,
            'name' => fake()->optional()->name(),
            'email' => fake()->optional()->safeEmail(),
        ];
    }

    /**
     * Indicate that the subscriber's phone is verified.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'phone_verified_at' => now(),
        ]);
    }

    /**
     * Indicate that the subscriber has a device registered for push.
     */
    public function withDevice(string $platform = 'ios'): static
    {
        return $this->state(fn (array $attributes) => [
            'device_token' => fake()->sha256(),
            'device_platform' => $platform,
        ]);
    }
}
