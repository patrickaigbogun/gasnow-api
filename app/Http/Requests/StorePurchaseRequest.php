<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cylinder_size_id' => ['required', 'integer', 'exists:cylinder_sizes,id'],
            'purchase_kg_id' => ['required', 'integer', 'exists:purchase_kgs,id'],
            'delivery_time_id' => ['required', 'integer', 'exists:delivery_times,id'],
            'delivery_address' => ['required', 'string', 'max:500'],
        ];
    }
}
