<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PreferenceRequest extends FormRequest
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

            'hourly_rate' => ['required', 'numeric'],
            'currency' => ['required', 'string', 'size:3'],
            'week_start' => ['required', 'string', 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],

            'additional_properties' => ['required', 'array'],
            'additional_properties.*' => ['nullable'],

            'additional_properties.roundDurationTo' => ['nullable', 'integer', 'in:0,5,10,15,30,60'],
            'additional_properties.roundMethod' => ['nullable', 'string', 'in:up,down,nearest'],
            'additional_properties.invoiceName' => ['nullable', 'string', 'max:255'],
            'additional_properties.invoiceTitle' => ['nullable', 'string', 'max:255'],
            'additional_properties.invoiceAddress' => ['nullable', 'string', 'max:255'],
            'additional_properties.invoicePrimaryColor' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'additional_properties.weekStartDay' => 'week start day',
            'additional_properties.roundDurationTo' => 'round duration to',
            'additional_properties.roundMethod' => 'round method',
            'additional_properties.invoiceName' => 'invoice name',
            'additional_properties.invoiceTitle' => 'invoice title',
            'additional_properties.invoiceAddress' => 'invoice address',
            'additional_properties.invoicePrimaryColor' => 'invoice color',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'additional_properties.roundDurationTo' => 'The round duration to must be one of: 0, 5, 10, 15, 30, or 60 minutes.',
            'additional_properties.roundMethod.in' => 'The round method must be one of: up, down, or nearest.',
            'week_start.in' => 'The week start day must be a valid day of the week.',
            'currency.size' => 'The default currency must be a 3-letter currency code.',
            'additional_properties.invoicePrimaryColor.regex' => 'The invoice color must be a valid hex color code.',
        ];
    }
}
