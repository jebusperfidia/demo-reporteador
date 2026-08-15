<?php

namespace Database\Factories;

use App\Models\Sale;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sale>
 */
class SaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $total = fake()->randomFloat(2, 500, 15000); // Ventas entre $500 y $15,000

        return [
            'customer_id' => Customer::factory(),
            'total_amount' => $total,
            'balance' => $total, // Inicia con el adeudo total
            'status' => fake()->randomElement(['pending', 'paid', 'canceled']),
        ];
    }
}
