<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterDoctorRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'             => 'required|string|max:255',
            'email'            => 'required|string|email|max:255|unique:users',
            'password'         => 'required|string|min:8',
            'user_type'        => 'required|string|in:doctor,pharmacist',
            'department'       => 'required|string|max:255',
            'phone'            => ['string' , 'required' , 'regex:/^\+[1-9]\d{1,14}$/','unique:users,phone'],
            'syndicate_number' => 'required|string|max:50|unique:doctors',
            'work_place_name'  => 'required|string|max:255',
            'address' => 'required|string|min:15|max:500',
            'landline_phone' => ['nullable','string','regex:/^0\d{8,10}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'رقم الجوال يجب أن يكون بالصيغة الدولية مثل 963+ ثم رقمك',
            'phone.unique' => 'رقم الجوال هذا مسجل به حساب آخر بالفعل.',
            'landline_phone.regex' => 'رقم الهاتف الأرضي يجب أن يكون بصيغة مثل 011 ثم رقم الأرضي',
            'address' => 'يرجي إدخال العنوان بشكل مفصل (المحافظة - المنطقة - الشارع - البناء) لتسهيل الوصول'
        ];
    }
}
