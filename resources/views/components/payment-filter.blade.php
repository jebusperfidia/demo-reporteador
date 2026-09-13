<div class="p-4 bg-gray-50 dark:bg-zinc-800/50 rounded-lg border border-gray-200 dark:border-zinc-700 mb-4 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4"
    x-data="{
        tempStart: '',
        tempEnd: '',
        error: '',
        apply() {
            this.error = '';
            if (!this.tempStart || !this.tempEnd) {
                this.error = 'Selecciona ambas fechas.';
                return;
            }
            if (this.tempStart > this.tempEnd) {
                this.error = 'La fecha inicial no puede ser mayor a la final.';
                return;
            }
            // Dispara el método filterDates en PaymentTable
            $wire.filterDates(this.tempStart, this.tempEnd);
        },
        clear() {
            this.tempStart = '';
            this.tempEnd = '';
            this.error = '';
            // Dispara el método resetDateFilter en PaymentTable
            $wire.resetDateFilter();
        }
     }">

    <div>
        <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Filtrar por Rango
            de Fecha</span>
    </div>

    <div class="flex flex-col items-end w-full lg:w-auto">
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full">
            <div class="w-full sm:w-40">
                <flux:input type="date" label="Desde" x-model="tempStart" />
            </div>
            <div class="w-full sm:w-40">
                <flux:input type="date" label="Hasta" x-model="tempEnd" />
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto mt-2 sm:mt-0 relative">
                <flux:button variant="primary" size="sm" @click="apply">Aplicar</flux:button>
                <flux:button variant="ghost" size="sm" @click="clear">Limpiar</flux:button>

                <!-- Loader integrado al lado de los botones -->
                <div wire:loading wire:target="filterDates, resetDateFilter" class="absolute -right-8">
                    <svg class="animate-spin h-5 w-5 text-indigo-600 dark:text-indigo-400"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>

        <span x-show="error" x-text="error" class="text-red-500 text-xs mt-2 font-medium" style="display: none;"></span>
    </div>
</div>