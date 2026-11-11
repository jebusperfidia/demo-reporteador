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
    public function updatedMonthsToShow(): void
    {
        $chartData = $this->getChartData();

        $this->dispatch(
            'update-chart',
            labels: $chartData['labels'],
            sales: $chartData['sales'],
            payments: $chartData['payments'],
            statusLabels: $chartData['statusLabels'],
            statusCounts: $chartData['statusCounts']
        );
    }

    private function getChartData(): array
    {
        $chartLabels = [];
        $salesData = [];
        $paymentsData = [];

        for ($i = $this->monthsToShow - 1; $i >= 0; $i--) {

            $month = Carbon::now()->startOfMonth()->subMonths($i);

            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $chartLabels[] = ucfirst($month->translatedFormat('M Y'));

            $salesData[] = Sale::query()
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->where('status', '!=', 'canceled')
                ->sum('total_amount');

            $paymentsData[] = Payment::query()
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->whereHas('sale', fn($query) => $query->where('status', '!=', 'canceled'))
                ->sum('amount');
        }

        $startDate = Carbon::now()
            ->startOfMonth()
            ->subMonths($this->monthsToShow - 1);

        $statuses = Sale::query()
            ->whereBetween('created_at', [$startDate, now()->endOfMonth()])
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'labels' => $chartLabels,
            'sales' => $salesData,
            'payments' => $paymentsData,

            'statusLabels' => [
                'Pagadas',
                'Pendientes',
                'Canceladas',
            ],

            'statusCounts' => [
                (int) ($statuses['paid'] ?? 0),
                (int) ($statuses['pending'] ?? 0),
                (int) ($statuses['canceled'] ?? 0),
            ],
        ];
    }
    public function render(): View
    {
        $startDate = Carbon::now()
            ->startOfMonth()
            ->subMonths($this->monthsToShow - 1);

        $endDate = Carbon::now()->endOfMonth();

        $totalCustomers = Customer::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $totalSales = Sale::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'canceled')
            ->sum('total_amount');

        $totalPaid = Payment::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereHas('sale', fn($query) => $query->where('status', '!=', 'canceled'))
            ->sum('amount');

        $pendingBalance = Sale::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'pending')
            ->sum('balance');

        return view('livewire.dashboard-stats', [
            'totalCustomers' => $totalCustomers,
            'totalSales' => $totalSales,
            'totalPaid' => $totalPaid,
            'pendingBalance' => $pendingBalance,
            'chartData' => $this->getChartData(),
        ]);
    }
}
