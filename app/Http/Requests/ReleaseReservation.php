<?php

namespace App\Http\Requests;

class ReleaseReservation extends Reservation
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'items' => 'required|array',
            'items.*.sku' => 'required|string',
            'items.*.order_id' => 'required|integer'
        ];
    }

    public function messages()
    {
        return [
            'items.required' => 'The items field is required.',
            'items.array' => 'The items must be an array.',
            'items.*.sku.required' => 'The SKU of each item is required.',
            'items.*.sku.string' => 'The SKU of each item must be a string.',
            'order_id.required' => 'The order ID is required.',
            'order_id.integer' => 'The order ID must be an integer.',
        ];
    }
}
