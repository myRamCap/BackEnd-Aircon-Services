<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'client_id' => 'required|integer',
            'vehicle_name' => 'required|string',
            'chassis_number' => 'nullable|string',
            'contact_number' => 'required|string',
            'make' => 'nullable|string',
            'model' => 'nullable|string',
            'year' => 'nullable|integer',
            'image' => 'string|nullable',
            'notes' => 'string|nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'client_id' => 'The Client name field is required.',
        ];
    }
}
