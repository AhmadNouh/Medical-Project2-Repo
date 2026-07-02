<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'notes'              => ['nullable', 'string', 'max:500'],
            'items'              => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity'   => ['required', 'integer', 'min:1'],
        ];
    }
    
    public function messages(): array
    {
        return [
            'items.required' => 'يجب إضافة منتجات إلى السلة لإتمام الطلب.',
            'items.*.product_id.exists' => 'أحد المنتجات المحددة غير موجود في النظام.',
            'items.*.quantity.min' => 'أقل كمية يمكن طلبها هي قطعة واحدة.',
        ];
    }
}