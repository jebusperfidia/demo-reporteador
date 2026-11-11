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
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class PaymentTable extends PowerGridComponent
{
    public string $tableName = 'paymentTable';

    public ?string $startDate = null;
    public ?string $endDate = null;

    use WithExport;

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

            PowerGrid::exportable(fileName: 'historial-abonos')
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV)
                ->stripTags(true),

            PowerGrid::footer()
                ->showPerPage(10, [10, 25, 50, 100, 0])
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        $query = Payment::query()
            ->join('sales', 'sales.id', '=', 'payments.sale_id')
            ->join('customers', 'customers.id', '=', 'sales.customer_id')
            ->select('payments.*')
            ->addSelect('customers.name as customer_name');

        if (!empty($this->startDate) && !empty($this->endDate)) {
            $start = Carbon::parse($this->startDate)->startOfDay();
            $end = Carbon::parse($this->endDate)->endOfDay();

            $query->whereBetween('payments.created_at', [$start, $end]);
        }

        return $query;
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('sale_id')
            ->add('customer_name')
            ->add('amount_formatted', fn($model) => '$' . number_format((float) $model->amount, 2))
            ->add('created_at_formatted', fn($model) => Carbon::parse($model->created_at)->format('d/m/Y H:i'))
            ->add('updated_at_formatted', fn($model) => Carbon::parse($model->updated_at)->format('d/m/Y H:i'))
            ->add('pdf_action', function ($row) {
                return view('livewire.partials.actions.pdf-btn', ['id' => $row->id])->render();
            });
    }

    public function columns(): array
    {
        return [
            Column::make('Folio', 'id')
                ->sortable(),

            Column::make('Folio Venta', 'sale_id')
                ->searchable()
                ->sortable(),

            Column::make('Cliente', 'customer_name', 'customers.name')
                ->searchable()
                ->sortable(),

            Column::make('Monto Abonado', 'amount_formatted', 'payments.amount')
                ->sortable()
                ->searchable()
                ->visibleInExport(false),

            Column::make('Monto Abonado', 'amount')
                ->hidden()
                ->visibleInExport(true),

            Column::make('Fecha de Registro', 'created_at_formatted', 'payments.created_at')
                ->sortable()
                ->visibleInExport(false),

            Column::make('Fecha de Registro', 'created_at')
                ->hidden()
                ->visibleInExport(true),

            Column::make('Acciones', 'pdf_action')
                ->bodyAttribute('text-center')
                ->visibleInExport(false),
        ];
    }

    public function openPdf($id)
    {
        $this->dispatch('open-pdf-modal', id: $id);
    }
}
