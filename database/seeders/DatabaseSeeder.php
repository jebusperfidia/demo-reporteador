<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\Sale;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */

    public function run(): void
    {
        DB::transaction(function () {

            // Limpiar exclusivamente los datos de demostración
            DB::table('payments')->delete();
            DB::table('sales')->delete();
            DB::table('customers')->delete();

            // Crear 120 clientes mexicanos
            Customer::factory(120)->create()->each(function ($customer) {

                // Entre 4 y 7 ventas por cliente
                $salesCount = random_int(4, 7);

                for ($i = 0; $i < $salesCount; $i++) {

                    // Distribuir las ventas durante los últimos 12 meses
                    $createdAt = Carbon::instance(
                        fake()->dateTimeBetween(
                            now()->copy()->subMonths(12),
                            now()
                        )
                    );

                    $dueDate = $createdAt->copy()->addDays(
                        fake()->randomElement([15, 30, 45, 60])
                    );

                    // Distribución de estados
                    $status = fake()->randomElement([
                        'paid',
                        'paid',
                        'paid',
                        'pending',
                        'pending',
                        'canceled',
                    ]);

                    // Importes entre $500 y $150,000 MXN
                    $totalCents = random_int(50000, 15000000);
                    $remainingCents = $totalCents;

                    $sale = Sale::factory()->create([
                        'customer_id' => $customer->id,
                        'total_amount' => $totalCents / 100,
                        'balance' => $totalCents / 100,
                        'status' => $status,
                        'due_date' => $dueDate->toDateString(),
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);

                    // Las ventas pagadas pueden tener entre 1 y 3 abonos.
                    // Las pendientes pueden tener entre 0 y 2.
                    $paymentCount = match ($status) {
                        'paid' => random_int(1, 3),
                        'pending' => random_int(0, 2),
                        default => 0,
                    };

                    for ($j = 0; $j < $paymentCount; $j++) {

                        if ($status === 'paid') {

                            $paymentsLeft = $paymentCount - $j;

                            $amountCents = $paymentsLeft === 1
                                ? $remainingCents
                                : random_int(
                                    100,
                                    $remainingCents - (($paymentsLeft - 1) * 100)
                                );
                        } else {

                            // En ventas pendientes siempre queda saldo.
                            $maxAmountCents = min(
                                $remainingCents - 100,
                                (int) floor($totalCents * 0.60)
                            );

                            if ($maxAmountCents < 100) {
                                break;
                            }

                            $amountCents = random_int(100, $maxAmountCents);
                        }

                        $paymentDate = Carbon::instance(
                            fake()->dateTimeBetween($createdAt, now())
                        );

                        Payment::factory()->create([
                            'sale_id' => $sale->id,
                            'amount' => $amountCents / 100,
                            'created_at' => $paymentDate,
                            'updated_at' => $paymentDate,
                        ]);

                        $remainingCents -= $amountCents;
                    }

                    // Las canceladas no generan cartera pendiente.
                    $finalBalance = $status === 'canceled'
                        ? 0
                        : $remainingCents / 100;

                    $sale->update([
                        'balance' => $finalBalance,
                    ]);
                }
            });
        });
    }
}
