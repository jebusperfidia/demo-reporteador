<x-table>
    <x-slot name="header">
    </x-slot>

    <x-slot name="body">
        @foreach ($payments as $payment)
            <x-table-row>
                <x-table-cell>{{ $payment->id }}</x-table-cell>
                <x-table-cell>{{ $payment->sale_id }}</x-table-cell>
                <x-table-cell>{{ $payment->amount }}</x-table-cell>
                <x-table-cell>{{ $payment->created_at_formatted }}</x-table-cell>
                <x-table-cell>
                    <button wire:click="pdf({{ $payment->id }})">
                        <svg class="w-5 h-5 text-red-500 hover:text-red-700 transition-colors" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                            </path>
                        </svg>
                    </button>
                </x-table-cell>
            </x-table-row>
        @endforeach
    </x-slot>

    <x-slot name="footer">
        {{ $payments->links() }}
    </x-slot>
</x-table>
