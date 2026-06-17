<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInternationalTripRealizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'international_trip_id' => 'required|exists:international_trip,id',
            'transfer_evidence' => 'required|file|mimes:pdf,xlsx,xls,jpg,jpeg,png|max:5120',
            'other_document' => 'sometimes|file|mimes:pdf,xlsx,xls,jpg,jpeg,png|max:15360',
        ];
    }
}
