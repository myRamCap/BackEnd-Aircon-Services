<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceCenterRequest extends FormRequest
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
            'name' => 'required|string|unique:service_centers,name',
            'category' => 'required|string',
            'country' => 'required|string',
            'house_number' => 'required|string',
            'barangay' => 'required|string',
            'municipality' => 'required|string',
            'province' => 'required|string',
            'longitude' => 'required|numeric|regex:/^\d{0,4}\.\d{1,15}$/',
            'latitude' => 'required|numeric|regex:/^\d{0,4}\.\d{1,15}$/',
            'facility' => 'required|integer',
            'corporate_manager_id' => 'required|integer',
            'image' => 'string|nullable',
        ];
    }
}
