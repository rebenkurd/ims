<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseItem>
 */
class PurchaseItemFactory extends Factory
{
    protected $model = PurchaseItem::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'purchase_id' => Purchase::inRandomOrder()->first()->id,
            'product_id' => Product::inRandomOrder()->first()->id,
            'quantity' => $this->faker->randomDigit,
            'unit_price' => $this->faker->randomFloat(2, 5, 50),
            'total_price' => $this->faker->randomFloat(2, 50, 200),
            'tax' => $this->faker->randomDigit,
            'discount' => $this->faker->randomDigit,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
