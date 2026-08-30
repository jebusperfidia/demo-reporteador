<x-layouts::app :title="__('Dashboard')">
    <flux:card class="mb-6">
        <div class="mb-4">
            <flux:heading size="lg">Historial de Abonos</flux:heading>
            <flux:subheading>Detalle de los pagos registrados en el sistema y progreso de cobranza.</flux:subheading>
        </div>

        <!-- Tu tabla renderizada dentro de la tarjeta -->
        <livewire:payment-table />
    </flux:card>
</x-layouts::app>