<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationRequest extends FormRequest
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
            // 'corporate_id' => 'required_without:service_center_id|integer|nullable:notifications,corporate_id,'.$this->id,
            // 'service_center_id' => 'required_without:corporate_id|integer|nullable',
            'category' => 'required|string:notifications,category,'.$this->id,
            'service_center' => ($category == 'SELECTED') ? 'required|array' : 'nullable',
            'datefrom' => 'required|string',
            'dateto' => 'required|string',
            'title' => 'required|string',
            'content' => 'required|string',
            'image_url' => 'required|string' 
        ];
    }

    public function messages(): array
    {
        return [
            'category.required' => 'You need to select "ALL SERVICE CENTER" or "CHOOSE SERVICE CENTER"',
        ];
    }
}
