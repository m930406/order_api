<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
            'id'    =>  'required|string|unique:orders,order_id',
            'name'  =>  'required|string',
            'address.city'   =>  'required|string',
            'address.district'   =>  'required|string',
            'address.street' =>  'required|string',
            'price' =>  'required|numeric',
            'currency'  =>  'required|in:TWD,USD,JPY,RMB,MYR',
        ];
    }

    protected function passedValidation()
    {
        $this->merge([
            'address' => json_encode($this->address),
            'order_id' => $this->id
        ]);

        $this->offsetUnset('id');
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
