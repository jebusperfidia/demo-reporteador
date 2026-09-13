<x-layouts::app :title="__('Dashboard')">

    <livewire:dashboard-stats />

    <flux:card class="mb-6" x-data="{
        start: '',
        end: '',
        error: '',
        isLoading: false,
        init() {
            Livewire.hook('commit', ({ component, succeed }) => {
                succeed(() => {
                    if (component.name === 'payment-table') {
                        this.isLoading = false;
                    }
                })
            });
        },
        apply() {
            this.error = '';
            if (!this.start || !this.end) {
                this.error = 'Selecciona ambas fechas.';
                return;
            }
            if (this.start > this.end) {
                this.error = 'La fecha inicial no puede ser mayor a la final.';
                return;
            }
    
            this.isLoading = true;
            Livewire.dispatchTo('payment-table', 'applyDateFilter', { start: this.start, end: this.end });
        },
        clear() {
            this.start = '';
            this.end = '';
            this.error = '';
    
            this.isLoading = true;
            Livewire.dispatchTo('payment-table', 'clearDateFilter');
        }
    }">

        <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center mb-6 gap-6">
            <div>
                <flux:heading size="lg">Historial de Abonos</flux:heading>
                <flux:subheading>Detalle de los pagos registrados en el sistema y progreso de cobranza.
                </flux:subheading>
            </div>

            <div
                class="w-full xl:w-auto flex flex-col bg-gray-50 dark:bg-zinc-800/50 p-5 sm:p-6 rounded-lg border border-gray-200 dark:border-zinc-700 shadow-sm">

                <span class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-4 uppercase tracking-wider">
                    Filtrar por Rango de Fechas
                </span>

                <div class="flex flex-col sm:flex-row items-end gap-5">
                    <div class="w-full sm:w-64">
                        <flux:input type="date" label="Desde" x-model="start" />
                    </div>
                    <div class="w-full sm:w-64">
                        <flux:input type="date" label="Hasta" x-model="end" />
                    </div>

                    <div class="flex items-center gap-3 w-full sm:w-auto mt-4 sm:mt-0">
                        <flux:button variant="primary" @click="apply" class="cursor-pointer">Aplicar</flux:button>
                        <flux:button variant="ghost" @click="clear" class="cursor-pointer">Limpiar</flux:button>
                    </div>
                </div>

                <span x-show="error" x-text="error" class="text-red-500 text-sm mt-3 font-medium"
                    style="display: none;"></span>
            </div>
        </div>

        <div class="relative">
            <div x-show="isLoading" x-transition.opacity
                class="absolute inset-0 bg-white/60 dark:bg-zinc-900/60 z-10 flex items-center justify-center rounded-lg backdrop-blur-[2px]"
                style="display: none;">
                <div
                    class="flex items-center gap-2 text-indigo-600 dark:text-indigo-400 font-semibold text-sm bg-white dark:bg-zinc-800 px-5 py-2.5 rounded-full shadow-md border border-gray-200 dark:border-zinc-700">
                    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span>Actualizando tabla...</span>
                </div>
            </div>

            <livewire:payment-table />
        </div>
    </flux:card>

</x-layouts::app>
