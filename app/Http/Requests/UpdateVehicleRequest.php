<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVehicleRequest extends FormRequest
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
            'client_id' => 'required|integer:vehicles,client_id,'.$this->id,
            'vehicle_name' => 'required|string',
            'chassis_number' => 'nullable|string',
            // 'contact_number' => 'nullable|string',
            'make' => 'required|string',
            'model' => 'nullable|string',
            'year' => 'nullable|integer',
            'image' => 'nullable|nullable',
            'notes' => 'nullable|nullable',
        ];
    }
}
