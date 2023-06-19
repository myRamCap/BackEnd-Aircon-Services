<?php

namespace App\Http\Requests;

use App\Models\TimeSlot;
use Illuminate\Foundation\Http\FormRequest;

class StoreTimeSlotRequest extends FormRequest
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
        $time = $this->input('time');

        return [
            'service_center_id' => 'required|integer',
            'time' => [
            'required',
            'string',
            function ($attribute, $value, $fail) use ($serviceCenterId, $time) {
                $existingRecord = TimeSlot::where('time', $time)
                    ->where('service_center_id', '=', $serviceCenterId)
                    ->first();

                if ($existingRecord) {
                    $fail("The time has already been taken.");
                }
            },
        ],
            // 'max_limit' => 'required|integer',
        ];
    }
}
