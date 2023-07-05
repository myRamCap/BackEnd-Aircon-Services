<?php

namespace App\Http\Requests;

use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
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
        $service = $this->input('name');
        return [
            'name' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($serviceCenterId, $service) {
                    $existingRecord = Service::where('name', $service)
                        ->where('service_center_id', '=', $serviceCenterId)
                        ->first();
    
                    if ($existingRecord) {
                        $fail("The name has already been taken.");
                    }
                },
            ],
            // 'name' => 'required|string|unique:services,name',
            'details' => 'required|string',
            'service_center_id' => 'required|integer',
            'image_id' => 'integer|nullable',
            'created_by' => 'integer|nullable',
        ];
    }
}
