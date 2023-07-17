<?php

namespace App\Http\Requests;

use App\Models\ServiceCost;
use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceCostRequest extends FormRequest
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
            'service_center_id' => 'required|integer',
            'service_id' => 'required|integer|unique:service_costs,service_id,'.$this->id,
            'cost' => 'required|integer',
            'markup' => 'required|integer',
            'notes' => 'nullable',
        ];
    }
}
