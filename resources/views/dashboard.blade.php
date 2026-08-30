<x-layouts::app :title="__('Dashboard')">

    <livewire:dashboard-stats />

    <flux:card class="mb-6">
        <!-- Encabezado con Flexbox para alinear el título y el filtro -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-4">
            <div>
                <flux:heading size="lg">Historial de Abonos</flux:heading>
                <flux:subheading>Detalle de los pagos registrados en el sistema y progreso de cobranza.
                </flux:subheading>
            </div>

            <!-- DatePicker de Flux enviando el dato a Livewire al hacer clic -->
            <div class="w-full sm:w-72">
                <flux:input icon="calendar-days" placeholder="Filtrar por fechas..." x-data x-init="flatpickr($el, {
                        mode: 'range',
                        dateFormat: 'd/m/Y',
                        enableTime: false,
                        locale: 'es',
                        onChange: function(selectedDates, dateStr) {
                            Livewire.dispatch('updateDateRange', { range: dateStr });
                        }
                    })" />
            </div>
        </div>

        <!-- Tu tabla renderizada -->
        <livewire:payment-table />
    </flux:card>

</x-layouts::app>