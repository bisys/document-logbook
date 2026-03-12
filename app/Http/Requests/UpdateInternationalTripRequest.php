<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInternationalTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cost_center_id' => 'required|exists:cost_centers,id',
            'itar_form' => 'sometimes|file|mimes:pdf|max:500',
            'document_number' => 'required|string|max:255|unique:international_trip,document_number,' . $this->route('internationalTrip')->id,
            'internal_memo' => 'sometimes|file|mimes:pdf|max:500',
            'summary_bussiness_trip' => 'sometimes|file|mimes:pdf|max:500',
            'overseas_allowance_form' => 'sometimes|file|mimes:pdf|max:500',
            'bussiness_trip_allowance' => 'sometimes|file|mimes:pdf|max:500',
            'rate' => 'sometimes|file|mimes:pdf|max:500',
            'budget_plan' => 'sometimes|file|mimes:pdf|max:500',
        ];
    }
}
