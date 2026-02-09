<?php

namespace App\Http\Requests;

use App\Enums\TimeFrameStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TimeFrameRequest extends FormRequest
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
        $isUpdate = $this->route('id') !== null;

        return [
            'project_id' => [Rule::requiredIf(! $isUpdate), 'exists:projects,id'],
            'name' => ['nullable', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['sometimes', 'required', Rule::enum(TimeFrameStatusEnum::class)],
            'notes' => ['nullable', 'string'],

            'hourly_rate' => ['required', 'numeric'],
            'currency' => ['required', 'string', 'size:3'],
        ];
    }
}
