<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    protected $model = Role::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' =>  $this->faker->randomElement(['super admin', 'admin',  'seller', 'purchaser']),
            'description' => $this->faker->text,
            'created_at' => now(),
            'updated_at' => now(),
            'status' => true,
        ];
    }
}
