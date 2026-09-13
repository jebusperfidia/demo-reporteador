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

        <!-- Escudo wire:ignore y limpieza de RAM con destroy() -->
        <div wire:ignore class="w-full h-80" x-data="{
            chartInstance: null,
            init() {
                // Cargamos la gráfica inicial
                this.drawChart({{ json_encode($chartData) }});
            },
            drawChart(dataObj) {
                // 1. LA MAGIA ANTI-LAG: Si ya hay una gráfica, mátala para liberar RAM
                if (this.chartInstance) {
                    this.chartInstance.destroy();
                }
        
                // 2. Dibuja la nueva gráfica limpia
                let ctx = this.$refs.canvas.getContext('2d');
                this.chartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: dataObj.labels,
                        datasets: [{
                                label: 'Total de Ventas',
                                data: dataObj.sales,
                                backgroundColor: '#3b82f6',
                                borderRadius: 4
                            },
                            {
                                label: 'Abonos Recibidos',
                                data: dataObj.payments,
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
        }" @update-chart.window="drawChart($event.detail)">
            <canvas x-ref="canvas"></canvas>
        </div>
    </flux:card>
</div>
