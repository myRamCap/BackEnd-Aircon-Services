<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'client_id' => 'required|integer:service_centers,name,'.$this->id,
            'vehicle_id' => 'required|integer',
            'services_id' => 'required|integer',
            'service_center_id' => 'required|integer',
            'contact_number' => 'string|nullable',
            'status' => 'required|string',
            'booking_date' => 'required|string',
            'time' => 'required|string',
            'notes' => 'string',
        ];
    }

    public function messages(): array
    {
        return [
            'client_id' => 'The Client name field is required.',
        ];
    }
}
