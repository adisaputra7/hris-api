<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'gender' => $this->faker->randomElement(['FEMALE', 'MALE']),
            'phone' => $this->faker->phoneNumber(),
            'age' => $this->faker->numberBetween(18, 60),
            'photo' => 'https://via.placeholder.com/150',
            'team_id' => $this->faker->numberBetween(1, 40),
            'role_id' => $this->faker->numberBetween(1, 100),
        ];
    }
}
