<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Courier>
 */
class CourierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'driver_license' => mt_rand(10000000000000, 99999999999999),
            'photo' => "https://picsum.photos/200/300",
            'phone' => fake()->e164PhoneNumber(),
            'address' => fake()->address(),
            'level' => rand(1, 5),
            'active' => true,
        ];
    }
}
