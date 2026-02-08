<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TimeEntryRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'time_frame_id' => ['required', 'string', 'exists:time_frames,id'],
            //            'work_day' => ['sometimes', 'required', 'date'],
            'start_time' => ['required', 'date'],
            'end_time' => ['required', 'date', 'after:start_time'],
            'description' => ['nullable', 'string'],
            'billable' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'time_frame_id.required' => 'The time_frame_id field was not provided. Please contact support if this was not intentional.',
        ];
    }
}
