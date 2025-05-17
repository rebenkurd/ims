<?php

namespace Database\Factories;

use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Purchase>
 */
class PurchaseFactory extends Factory
{
    protected $model = Purchase::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reference_no' => $this->faker->unique()->numerify('#####'),
            'purchase_code' => $this->faker->unique()->numerify('PR_#####'),
            'supplier_id' => Supplier::inRandomOrder()->first()->id,
            'total' => $this->faker->randomFloat(2, 50, 1000),
            'discount' => $this->faker->randomFloat(2, 0, 50),
            'created_by' => User::inRandomOrder()->first()->id,
            'created_at' => now(),
            'updated_at' => now(),
            'status' => true
        ];
    }
}
