<div>
    <!-- Encabezado con Selector Dinámico -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <flux:heading size="xl">Resumen Financiero</flux:heading>
            <flux:subheading>Indicadores basados en el rango seleccionado.</flux:subheading>
        </div>

        <div class="w-full sm:w-56">
            <flux:select wire:model.live="monthsToShow">
                <option value="3">Últimos 3 meses</option>
                <option value="6">Últimos 6 meses</option>
                <option value="12">Último año</option>
            </flux:select>
        </div>
    </div>

    <!-- Fila de Tarjetas (KPIs). Se actualizan solas gracias a Livewire -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <flux:card>
            <flux:heading size="lg">Nuevos Clientes</flux:heading>
            <flux:subheading class="text-3xl font-bold mt-2">
                {{ $totalCustomers }}
            </flux:subheading>
        </flux:card>

        <flux:card>
            <flux:heading size="lg">Ventas en el Periodo</flux:heading>
            <flux:subheading class="text-3xl font-bold text-blue-600 dark:text-blue-400 mt-2">
                ${{ number_format($totalSales, 2) }}
            </flux:subheading>
        </flux:card>

        <flux:card>
            <flux:heading size="lg">Total Cobrado</flux:heading>
            <flux:subheading class="text-3xl font-bold text-green-600 dark:text-green-400 mt-2">
                ${{ number_format($totalPaid, 2) }}
            </flux:subheading>
        </flux:card>

        <flux:card>
            <flux:heading size="lg">Cartera Vencida</flux:heading>
            <flux:subheading class="text-3xl font-bold text-red-600 dark:text-red-400 mt-2">
                ${{ number_format($pendingBalance, 2) }}
            </flux:subheading>
        </flux:card>
    </div>

    <!-- Contenedor de la Gráfica -->
    <flux:card class="mb-6">
        <div class="mb-4">
            <flux:heading size="lg">Comparativo de Ingresos vs Ventas</flux:heading>
        </div>

        <!-- Motor de Alpine.js interceptando actualizaciones -->
        <div class="w-full h-80" x-data="{
                chartInstance: null,
                initChart() {
                    let ctx = this.$refs.canvas;
                    this.chartInstance = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: {{ json_encode($chartData['labels']) }},
                            datasets: [
                                {
                                    label: 'Total de Ventas',
                                    data: {{ json_encode($chartData['sales']) }},
                                    backgroundColor: '#3b82f6',
                                    borderRadius: 4
                                },
                                {
                                    label: 'Abonos Recibidos',
                                    data: {{ json_encode($chartData['payments']) }},
                                    backgroundColor: '#22c55e',
                                    borderRadius: 4
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { position: 'bottom' } },
                            scales: { y: { beginAtZero: true } }
                        }
                    });
                }
             }" x-init="initChart()" @update-chart.window="
                chartInstance.data.labels = $event.detail.labels;
                chartInstance.data.datasets[0].data = $event.detail.sales;
                chartInstance.data.datasets[1].data = $event.detail.payments;
                chartInstance.update();
             ">
            <canvas x-ref="canvas"></canvas>
        </div>
    </flux:card>
</div>