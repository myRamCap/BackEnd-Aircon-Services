<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
        $role_id = $this->role_id;

        if ($role_id == 2) {
            return [
                'email' => 'required|email|unique:users,email,'.$this->id,
                'first_name' => 'nullable',
                'last_name' => 'nullable',
                'contact_number' => 'required|string',
                'role_id' => 'required|integer',
                'allowed_sc' => 'required|integer',
                'allowed_bm' => 'required|integer',
            ];
        } else if ($role_id == 3) {
            return [
                'email' => 'required|email|unique:users,email,'.$this->id,
                'first_name' => 'nullable',
                'last_name' => 'nullable',
                'contact_number' => 'required|string',
                'role_id' => 'required|integer',
                'service_center_id' => 'required|integer',
            ];
        } else if ($role_id == 4) {
            return [
                'email' => 'required|email|unique:users,email,'.$this->id,
                'first_name' => 'nullable',
                'last_name' => 'nullable',
                'contact_number' => 'required|string',
                'role_id' => 'required|integer',
                'branch_manager_id' => 'required|integer',
            ];
        }
        
    }

    public function messages(): array
    {
        return [
            'branch_manager_id' => 'The Branch Manager field is required.',
            'service_center_id' => 'The Service Center field is required.',
        ];
    }
}
