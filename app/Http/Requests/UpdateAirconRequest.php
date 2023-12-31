<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAirconRequest extends FormRequest
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
            'client_id' => 'required|integer:aircons,client_id,'.$this->id,
            'aircon_name' => 'required|string',
            'aircon_type' => 'nullable|string',
            // 'contact_number' => 'nullable|string',
            'make' => 'required|string',
            'model' => 'nullable|string',
            'horse_power' => 'nullable|string',
            'serial_number' => 'nullable|string',
            'image' => 'nullable|nullable',
            'notes' => 'nullable|nullable',
        ];
    }
}
