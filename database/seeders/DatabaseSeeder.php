<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Sale;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        // Creamos 50 clientes
        Customer::factory(50)->create()->each(function ($customer) {

            // A cada cliente le generamos de 1 a 5 ventas
            Sale::factory(rand(1, 5))->create([
                'customer_id' => $customer->id
            ])->each(function ($sale) {

                // Lógica financiera realista
                if ($sale->status === 'paid') {
                    // Si está pagada, el pago es por el total y el saldo queda en 0
                    Payment::factory()->create([
                        'sale_id' => $sale->id,
                        'amount' => $sale->total_amount
                    ]);
                    $sale->update(['balance' => 0]);
                } elseif ($sale->status === 'pending') {
                    // Si está pendiente, hay un 50% de probabilidad de que tenga un abono parcial
                    if (rand(0, 1)) {
                        $partialAmount = round($sale->total_amount / 2, 2);
                        Payment::factory()->create([
                            'sale_id' => $sale->id,
                            'amount' => $partialAmount
                        ]);
                        $sale->update(['balance' => $sale->total_amount - $partialAmount]);
                    }
                }
            });
        });
    }
}
