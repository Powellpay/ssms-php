<?php

namespace App\Domain\Finance\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice_id' => $this->invoice_id,
            'student_id' => $this->student_id,
            'amount' => $this->amount,
            'payment_method' => $this->payment_method,
            'reference_no' => $this->reference_no,
            'payment_date' => $this->payment_date,
            'received_by' => $this->received_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
