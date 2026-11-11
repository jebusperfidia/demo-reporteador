<div>
    <flux:modal name="payment-pdf-modal" class="w-full max-w-2xl">
        @if($payment)
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 pr-12">
                <flux:heading size="xl" class="mb-2 sm:mb-0">Comprobante de Abono</flux:heading>
                <div class="text-left sm:text-right">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Folio:</span>
                    <span class="text-lg font-bold text-gray-900 dark:text-gray-100">#{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</span>
                </div>
            </div>

            <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-lg p-6 border border-gray-200 dark:border-zinc-700 shadow-sm space-y-6">
                
                <!-- Detalles de la empresa y fecha -->
                <div class="flex flex-col sm:flex-row justify-between border-b border-gray-200 dark:border-zinc-700 pb-4">
                    <div>
                        <h3 class="font-bold text-gray-800 dark:text-gray-200">Reporter Inc.</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">info@reporter.com</p>
                    </div>
                    <div class="text-left sm:text-right mt-4 sm:mt-0">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Fecha de Pago</p>
                        <p class="text-sm text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($payment->created_at)->format('d/m/Y H:i A') }}</p>
                    </div>
                </div>

                <!-- Información del Cliente -->
                <div>
                    <h4 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Cobrado a:</h4>
                    <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $payment->sale->customer->name }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $payment->sale->customer->email }}</p>
                    @if($payment->sale->customer->phone)
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $payment->sale->customer->phone }}</p>
                    @endif
                </div>

                <!-- Detalles de la Venta y Abono -->
                <div class="mt-6">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-zinc-700">
                                <th class="pb-2 text-sm font-bold text-gray-600 dark:text-gray-300">Descripción</th>
                                <th class="pb-2 text-sm font-bold text-gray-600 dark:text-gray-300 text-right">Monto</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <tr class="border-b border-gray-100 dark:border-zinc-700/50">
                                <td class="py-3 text-gray-800 dark:text-gray-200">Abono a Venta #{{ str_pad($payment->sale->id, 6, '0', STR_PAD_LEFT) }}</td>
                                <td class="py-3 text-right text-gray-900 dark:text-gray-100">${{ number_format($payment->amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="py-3 text-gray-500 dark:text-gray-400">Total de la Venta</td>
                                <td class="py-3 text-right text-gray-500 dark:text-gray-400">${{ number_format($payment->sale->total_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="py-1 text-gray-500 dark:text-gray-400">Saldo Restante</td>
                                <td class="py-1 text-right text-gray-500 dark:text-gray-400">${{ number_format($payment->sale->balance, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Total del Recibo -->
                <div class="flex justify-between items-center border-t border-gray-200 dark:border-zinc-700 pt-4 mt-4">
                    <span class="font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wide">Total Pagado</span>
                    <span class="text-2xl font-black text-indigo-600 dark:text-indigo-400">${{ number_format($payment->amount, 2) }}</span>
                </div>
            </div>

            <div class="flex justify-end space-x-2 rtl:space-x-reverse mt-6">
                <flux:modal.close>
                    <flux:button variant="ghost" class="cursor-pointer">Cerrar</flux:button>
                </flux:modal.close>
                <flux:button variant="primary" onclick="window.print()" class="cursor-pointer">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Imprimir
                </flux:button>
            </div>
        @else
            <div class="flex items-center justify-center p-6">
                <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        @endif
    </flux:modal>
    
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            [data-flux-modal] * {
                visibility: visible;
            }
            [data-flux-modal] {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 20px;
                box-shadow: none;
                border: none;
                background: white;
            }
            /* Hide buttons during print */
            [data-flux-modal] .flex.justify-end.space-x-2 {
                display: none !important;
            }
        }
        
        /* Hover effect for modal X close button */
        html body [data-flux-modal] dialog .absolute button[aria-label="Close modal"], 
        html body [data-flux-modal] dialog .absolute button[aria-label="Cerrar modal"] {
            transition: background-color 0.2s;
            border-radius: 9999px;
            padding: 4px;
            cursor: pointer;
        }
        html body [data-flux-modal] dialog .absolute button[aria-label="Close modal"]:hover,
        html body [data-flux-modal] dialog .absolute button[aria-label="Cerrar modal"]:hover {
            background-color: rgba(156, 163, 175, 0.2);
        }
    </style>
</div>
