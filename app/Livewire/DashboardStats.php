<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\Sale;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Livewire\Component;

class DashboardStats extends Component
{
    // Rango por defecto: Últimos 6 meses
    public int $monthsToShow = 6;

    // Se ejecuta automáticamente cuando el usuario cambia el selector en la vista
    public function updatedMonthsToShow()
    {
        $chartData = $this->getChartData();

        // Enviamos los nuevos datos a Alpine.js sin recargar la página
        $this->dispatch(
            'update-chart',
            labels: $chartData['labels'],
            sales: $chartData['sales'],
            payments: $chartData['payments']
        );
    }

    private function getChartData(): array
    {
        $chartLabels = [];
        $salesData = [];
        $paymentsData = [];

        for ($i = $this->monthsToShow - 1; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $chartLabels[] = ucfirst($month->translatedFormat('M Y'));

            $salesData[] = Sale::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum('total_amount');

            $paymentsData[] = Payment::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum('amount');
        }

        return [
            'labels' => $chartLabels,
            'sales' => $salesData,
            'payments' => $paymentsData,
        ];
    }

    public function render(): View
    {
        // Calculamos la fecha de inicio basándonos en el filtro seleccionado
        $startDate = Carbon::now()->subMonths($this->monthsToShow - 1)->startOfMonth();

        // Filtramos todas las métricas de las tarjetas según esa fecha
        $totalCustomers = Customer::where('created_at', '>=', $startDate)->count();
        $totalSales = Sale::where('created_at', '>=', $startDate)->sum('total_amount');
        $totalPaid = Payment::where('created_at', '>=', $startDate)->sum('amount');
        $pendingBalance = Sale::where('created_at', '>=', $startDate)->sum('balance');

        return view('livewire.dashboard-stats', [
            'totalCustomers' => $totalCustomers,
            'totalSales' => $totalSales,
            'totalPaid' => $totalPaid,
            'pendingBalance' => $pendingBalance,
            'chartData' => $this->getChartData(),
        ]);
    }
}
