<div>
    <!-- Encabezado con Selector Dinámico -->
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4 mb-7">
        <div>
            <h1 class="page-title">Resumen Financiero</h1>
            <p class="page-subtitle">
                Indicadores y análisis del periodo seleccionado.
            </p>
        </div>

        <div class="w-full sm:w-56">
            <label class="text-xs font-medium data-muted mb-2 block">
                Periodo de consulta
            </label>

            <flux:select wire:model.live="monthsToShow">
                <option value="3">Últimos 3 meses</option>
                <option value="6">Últimos 6 meses</option>
                <option value="12">Últimos 12 meses</option>
            </flux:select>
        </div>
    </div>

    <!-- Fila de Tarjetas (KPIs). Se actualizan solas gracias a Livewire -->
    <div class="dashboard-grid mb-5">

        {{-- Clientes --}}
        <div class="report-kpi-card flex items-start gap-4">
            <div class="kpi-icon kpi-icon--accent">
                <flux:icon name="user-group" class="size-5" />
            </div>

            <div class="min-w-0">
                <p class="kpi-label">Nuevos Clientes</p>
                <p class="kpi-value">{{ number_format($totalCustomers) }}</p>
                <p class="kpi-caption">Registrados en el periodo</p>
            </div>
        </div>

        {{-- Ventas --}}
        <div class="report-kpi-card flex items-start gap-4">
            <div class="kpi-icon kpi-icon--info">
                <flux:icon name="shopping-cart" class="size-5" />
            </div>

            <div class="min-w-0">
                <p class="kpi-label">Ventas del Periodo</p>
                <p class="kpi-value">${{ number_format($totalSales, 2) }}</p>
                <p class="kpi-caption">Sin operaciones canceladas</p>
            </div>
        </div>

        {{-- Cobranza --}}
        <div class="report-kpi-card flex items-start gap-4">
            <div class="kpi-icon kpi-icon--success">
                <flux:icon name="banknotes" class="size-5" />
            </div>

            <div class="min-w-0">
                <p class="kpi-label">Total Cobrado</p>
                <p class="kpi-value">${{ number_format($totalPaid, 2) }}</p>
                <p class="kpi-caption">Abonos recibidos en el periodo</p>
            </div>
        </div>

        {{-- Saldo pendiente --}}
        <div class="report-kpi-card flex items-start gap-4">
            <div class="kpi-icon kpi-icon--danger">
                <flux:icon name="clock" class="size-5" />
            </div>

            <div class="min-w-0">
                <p class="kpi-label">Saldo Pendiente</p>
                <p class="kpi-value">${{ number_format($pendingBalance, 2) }}</p>
                <p class="kpi-caption">De ventas pendientes</p>
            </div>
        </div>

    </div>

    <!-- Contenedor de la Gráfica -->

    <div class="dashboard-chart-grid mb-6">

        {{-- Ventas contra cobranza --}}
        <section class="report-panel dashboard-chart-main">
            <div class="mb-5">
                <h2 class="section-title">Ingresos vs Ventas por Mes</h2>
                <p class="section-subtitle">
                    Comparativo de ventas registradas y abonos recibidos.
                </p>
            </div>

            <div wire:ignore class="relative h-80 w-full" x-data="{
                chartInstance: null,
            
                init() {
                    this.drawChart(@js($chartData));
                },
            
                drawChart(dataObj) {
                    if (this.chartInstance) {
                        this.chartInstance.destroy();
                    }
            
                    const ctx = this.$refs.canvas.getContext('2d');
            
                    this.chartInstance = new Chart(ctx, {
                        type: 'line',
            
                        data: {
                            labels: dataObj.labels,
            
                            datasets: [{
                                    label: 'Ventas',
                                    data: dataObj.sales,
                                    borderColor: '#60a5fa',
                                    backgroundColor: 'rgba(96, 165, 250, 0.12)',
                                    fill: true,
                                    tension: 0.35,
                                    pointRadius: 3,
                                    pointHoverRadius: 5
                                },
                                {
                                    label: 'Cobrado',
                                    data: dataObj.payments,
                                    borderColor: '#4ade80',
                                    backgroundColor: 'rgba(74, 222, 128, 0.10)',
                                    fill: true,
                                    tension: 0.35,
                                    pointRadius: 3,
                                    pointHoverRadius: 5
                                }
                            ]
                        },
            
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
            
                            interaction: {
                                intersect: false,
                                mode: 'index'
                            },
            
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        color: '#a1aab8',
                                        usePointStyle: true,
                                        padding: 20
                                    }
                                }
                            },
            
                            scales: {
                                x: {
                                    grid: {
                                        color: 'rgba(148, 163, 184, 0.08)'
                                    },
                                    ticks: {
                                        color: '#94a3b8'
                                    }
                                },
            
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(148, 163, 184, 0.10)'
                                    },
                                    ticks: {
                                        color: '#94a3b8',
                                        callback: value => '$' + Number(value).toLocaleString('es-MX')
                                    }
                                }
                            }
                        }
                    });
                }
            }"
                @update-chart.window="drawChart($event.detail)">
                <canvas x-ref="canvas"></canvas>
            </div>
        </section>

        {{-- Distribución de ventas --}}
        <section class="report-panel">
            <div class="mb-5">
                <h2 class="section-title">Estado de las Ventas</h2>
                <p class="section-subtitle">
                    Distribución de operaciones del periodo.
                </p>
            </div>

            <div wire:ignore class="relative h-80 w-full" x-data="{
                chartInstance: null,
            
                init() {
                    this.drawChart(@js($chartData));
                },
            
                drawChart(dataObj) {
                    if (this.chartInstance) {
                        this.chartInstance.destroy();
                    }
            
                    const ctx = this.$refs.canvas.getContext('2d');
            
                    this.chartInstance = new Chart(ctx, {
                        type: 'doughnut',
            
                        data: {
                            labels: dataObj.statusLabels,
            
                            datasets: [{
                                data: dataObj.statusCounts,
                                backgroundColor: [
                                    '#22c55e',
                                    '#f59e0b',
                                    '#ef4444'
                                ],
                                borderWidth: 0,
                                hoverOffset: 6
                            }]
                        },
            
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '68%',
            
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        color: '#a1aab8',
                                        usePointStyle: true,
                                        padding: 18
                                    }
                                }
                            }
                        }
                    });
                }
            }"
                @update-chart.window="drawChart($event.detail)">
                <canvas x-ref="canvas"></canvas>
            </div>
        </section>

    </div>

</div>
