<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'username' => fake()->unique()->userName(),
            'email' => $this->faker->unique()->safeEmail(),
            'bio' => $this->faker->optional()->paragraph(),
            'image' => $this->faker->optional()->imageUrl(),
            'password' => Hash::make('p@S52024'),
            'created_at' => $createdAt = $this->faker->dateTimeThisMonth(),
            'updated_at' => $this->faker->optional(50, $createdAt)->dateTimeBetween($createdAt),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
