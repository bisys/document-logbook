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
            'itar_form' => 'sometimes|file|mimes:pdf,xlsx,xls,jpg,jpeg,png|max:5120',
            'document_number' => 'required|string|max:255|unique:international_trip,document_number,' . $this->route('internationalTrip')->id,
            'internal_memo' => 'sometimes|file|mimes:pdf,xlsx,xls,jpg,jpeg,png|max:5120',
            'summary_bussiness_trip' => 'sometimes|file|mimes:pdf,xlsx,xls,jpg,jpeg,png|max:5120',
            'overseas_allowance_form' => 'sometimes|file|mimes:pdf,xlsx,xls,jpg,jpeg,png|max:5120',
            'bussiness_trip_allowance' => 'sometimes|file|mimes:pdf,xlsx,xls,jpg,jpeg,png|max:5120',
            'rate' => 'sometimes|file|mimes:pdf,xlsx,xls,jpg,jpeg,png|max:5120',
            'budget_plan' => 'sometimes|file|mimes:pdf,xlsx,xls,jpg,jpeg,png|max:5120',
            'other_document' => 'sometimes|file|mimes:pdf,xlsx,xls,jpg,jpeg,png|max:15360',
        ];
    }
}
