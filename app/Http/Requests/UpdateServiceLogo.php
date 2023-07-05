<?php

namespace App\Http\Requests;

use App\Models\ServiceCost;
use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceLogo extends FormRequest
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
        $serviceCenterId = $this->input('service_center_id');
        $service = $this->input('service_id');

        return [
            'title' => 'required|string|unique:services_logos,title,'.$this->id,
            'description' => 'required|string',
            'image' => 'string',
            'image_url' => 'string',
            'updated_by' => 'integer',

            'service_center_id' => 'required|integer',
            'service_id' => 'required|integer|unique:services_logos,title',
            'service_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($serviceCenterId, $service) {
                    $existingRecord = ServiceCost::where('service_id', $service)
                        ->where('service_center_id', '=', $serviceCenterId)
                        ->first();
    
                    if ($existingRecord) {
                        $fail("The service has already been taken.");
                    }
                },
            ],
            'cost' => 'required|integer',
            'markup' => 'required|integer',
            'notes' => 'nullable',
        ];
    }
}
