<?php

namespace App\Http\Requests;

use App\Models\ServiceCenterService;
use Illuminate\Foundation\Http\FormRequest;

class StoreServiceCenterServicesRequest extends FormRequest
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
        $serviceCenterId = $this->input('service_center_id');
        $service = $this->input('service_id');
        return [
            'service_center_id' => 'required|integer',
            'service_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($serviceCenterId, $service) {
                    $existingRecord = ServiceCenterService::where('service_id', $service)
                        ->where('service_center_id', '=', $serviceCenterId)
                        ->first();
    
                    if ($existingRecord) {
                        $fail("The service name has already been taken.");
                    }
                },
            ],
            'estimated_time' => 'required|nullable|string',
            'estimated_time_desc' => 'nullable|string',
            'price' => 'nullable|integer',
        ];
    }
 
    public function messages(): array
    {
        return [
            'service_id.required' => 'The service name field is required.',
        ];
    }
}
