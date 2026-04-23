<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInternationalTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cost_center_id' => 'required|exists:cost_centers,id',
            'itar_form' => 'required|file|mimes:pdf,xlsx,xls,jpg,jpeg,png|max:500',
            'document_number' => 'required|string|max:255|unique:international_trip,document_number',
            'internal_memo' => 'required|file|mimes:pdf,xlsx,xls,jpg,jpeg,png|max:500',
            'summary_bussiness_trip' => 'required|file|mimes:pdf,xlsx,xls,jpg,jpeg,png|max:500',
            'overseas_allowance_form' => 'required|file|mimes:pdf,xlsx,xls,jpg,jpeg,png|max:500',
            'bussiness_trip_allowance' => 'required|file|mimes:pdf,xlsx,xls,jpg,jpeg,png|max:500',
            'rate' => 'required|file|mimes:pdf,xlsx,xls,jpg,jpeg,png|max:500',
            'budget_plan' => 'required|file|mimes:pdf,xlsx,xls,jpg,jpeg,png|max:500',
            'other_document' => 'sometimes|file|mimes:pdf,xlsx,xls,jpg,jpeg,png|max:500',
        ];
    }
}
