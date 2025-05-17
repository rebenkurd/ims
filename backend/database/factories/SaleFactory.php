<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sale>
 */
class SaleFactory extends Factory
{
    protected $model = Sale::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invoice_no' => $this->faker->unique()->numerify('INV-#####'),
            "sale_code" => $this->faker->unique()->numerify('PR#####'),
            'customer_id' => Customer::inRandomOrder()->first()->id,
            'total' => $this->faker->randomFloat(2, 50, 1000),
            'discount' => $this->faker->randomFloat(2, 0, 50),
            'created_by' => User::inRandomOrder()->first()->id,
            'created_at' => now(),
            'updated_at' => now(),
            'status' => true
        ];
    }
}
