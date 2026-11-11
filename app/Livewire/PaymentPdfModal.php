<?php

namespace App\Livewire;

use App\Models\Payment;
use Livewire\Component;
use Livewire\Attributes\On;

class PaymentPdfModal extends Component
{
    public ?Payment $payment = null;

    #[On('open-pdf-modal')]
    public function loadPayment($id)
    {
        $this->payment = Payment::with('sale.customer')->findOrFail($id);
        
        $this->modal('payment-pdf-modal')->show();
    }

    public function render()
    {
        return view('livewire.payment-pdf-modal');
    }
}
