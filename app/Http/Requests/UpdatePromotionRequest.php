<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePromotionRequest extends FormRequest
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
            'category' => 'required|string:promotions,category,'.$this->id,
            'client' => ($category == 'SELECTED') ? 'required|array' : 'nullable',
            'datefrom' => 'required',
            'dateto' => 'required',
            'title' => 'required',
            'content' => 'required',
            'image_url' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'category.required' => 'You need to select "ALL CLIENTS" or "CHOOSE CLIENT"',
        ];
    }
}
