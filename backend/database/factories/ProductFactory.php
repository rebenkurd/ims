<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->word,
            'name' => $this->faker->word,
            'brand_id' => Brand::inRandomOrder()->first()->id,
            'category_id' => Category::inRandomOrder()->first()->id,
            'unit' => $this->faker->word,
            'per_carton' => $this->faker->randomDigit,
            'minimum_qty' => $this->faker->randomDigit,
            'expire_date' => $this->faker->date(),
            'barcode' => $this->faker->unique()->ean13,
            'description' => $this->faker->text,
            'price' => $this->faker->randomFloat(2, 10, 100),
            'tax' => $this->faker->randomDigit,
            'purchase_price' => $this->faker->randomFloat(2, 5, 50),
            'tax_type' => $this->faker->word,
            'profit_margin' => $this->faker->randomFloat(2, 10, 50),
            'sales_price' => $this->faker->randomFloat(2, 50, 100),
            'final_price' => $this->faker->randomFloat(2, 50, 100),
            'discount_type' => $this->faker->word,
            'discount' => $this->faker->randomDigit,
            'current_opening_stock' => $this->faker->randomDigit,
            'adjust_stock' => $this->faker->randomDigit,
            'adjustment_note' => $this->faker->text,
            'created_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
            'status' => true
        ];
    }
}
