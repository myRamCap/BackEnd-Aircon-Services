<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceCenterOperationTimeRequest extends FormRequest
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
        $category = $this->input('category');
        return [
            'category' => 'required|string',
            'service_center_id' => 'required|integer',
            'opening_time' => ($category == 'custom_time') ? 'required' : 'nullable',
            'closing_time' => ($category == 'custom_time') ? 'required' : 'nullable',
            'monday' => 'nullable',
            'tuesday' => 'nullable',
            'wednesday' => 'nullable',
            'thursday' => 'nullable',
            'friday' => 'nullable',
            'saturday' => 'nullable',
            'sunday' => 'nullable',
        ];
    }
}
