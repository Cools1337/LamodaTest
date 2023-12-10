<?php

namespace App\Http\Requests;

class ReserveProduct extends Reservation
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
            'orders' => 'required|array',
            'orders.*.sku' => 'required|string',
            'orders.*.quantity' => 'required|integer|min:1',
            'orders.*.order_id' => 'required|integer|unique:product_reservations,order_id',
        ];
    }



    public function messages()
    {
        return [
            'orders.required' => 'The orders field is required.',
            'orders.array' => 'The orders must be an array.',
            'orders.*.sku.required' => 'The SKU of each item is required.',
            'orders.*.sku.string' => 'The SKU of each item must be a string.',
            'orders.*.quantity.required' => 'The quantity of each item is required.',
            'orders.*.quantity.integer' => 'The quantity of each item must be an integer.',
            'orders.*.quantity.min' => 'The quantity of each item must be at least :min.',
            'orders.*.order_id.required' => 'The order ID of each item is required.',
            'orders.*.order_id.integer' => 'The order ID of each item must be an integer.',
        ];
    }
}
