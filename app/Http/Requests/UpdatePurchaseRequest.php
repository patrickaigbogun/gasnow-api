<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cylinder_size_id' => ['sometimes', 'integer', 'exists:cylinder_sizes,id'],
            'purchase_kg_id' => ['sometimes', 'integer', 'exists:purchase_kgs,id'],
            'delivery_time_id' => ['sometimes', 'integer', 'exists:delivery_times,id'],
            'delivery_address' => ['sometimes', 'string', 'max:500'],
        ];
    }
}
