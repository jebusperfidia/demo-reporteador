<?php

namespace App\Livewire;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class PaymentTable extends PowerGridComponent
{
    public string $tableName = 'paymentTable';

    public ?string $startDate = null;
    public ?string $endDate = null;

    #[On('applyDateFilter')]
    public function filterDates($start, $end): void
    {
        $this->startDate = $start;
        $this->endDate = $end;
        $this->resetPage();
    }

    #[On('clearDateFilter')]
    public function resetDateFilter(): void
    {
        $this->startDate = null;
        $this->endDate = null;
        $this->resetPage();
    }

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::header()
                ->showSearchInput(),

            PowerGrid::footer()
                ->showPerPage(10, [10, 25, 50, 100])
                ->showRecordCount()
            // ->Pagination('vendor.livewire.tailwind'),
            // Al quitar la línea del 'simple-tailwind', forzamos a que use tu archivo publicado
        ];
    }

    public function datasource(): Builder
    {
        $query = Payment::query();

        if (!empty($this->startDate) && !empty($this->endDate)) {
            $start = Carbon::parse($this->startDate)->startOfDay();
            $end = Carbon::parse($this->endDate)->endOfDay();

            $query->whereBetween('created_at', [$start, $end]);
        }

        return $query;
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('sale_id')
            ->add('amount', fn($model) => '$' . number_format((float)$model->amount, 2))
            ->add('created_at_formatted', fn($model) => Carbon::parse($model->created_at)->format('d/m/Y H:i'))
            ->add('updated_at_formatted', fn($model) => Carbon::parse($model->updated_at)->format('d/m/Y H:i'));
    }

    public function columns(): array
    {
        return [
            Column::make('Folio', 'id')->sortable(),
            Column::make('Folio Venta', 'sale_id')->searchable()->sortable(),
            Column::make('Monto Abonado', 'amount', 'amount')->sortable()->searchable(),
            Column::make('Fecha de Registro', 'created_at_formatted', 'created_at')->sortable(),
            Column::action('Acciones')
        ];
    }

    public function actions($row): array
    {
        return [
            Button::add('pdf')
                ->slot('<svg class="w-5 h-5 text-red-500 hover:text-red-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>')
                ->id()
                ->class('flex items-center justify-center p-1.5 rounded hover:bg-gray-200 dark:hover:bg-gray-800 transition-colors outline-none')
                ->dispatch('open-pdf-modal', ['id' => $row->id])
        ];
    }
}
