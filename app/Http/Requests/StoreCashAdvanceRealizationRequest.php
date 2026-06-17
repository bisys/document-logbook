<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCashAdvanceRealizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cash_advance_draw_id' => 'required|exists:cash_advance_draw,id',
            'car_form' => 'required|file|mimes:pdf,xlsx,xls,jpg,jpeg,png|max:5120',
            'original_invoice' => 'required|file|mimes:pdf,xlsx,xls,jpg,jpeg,png|max:5120',
            'copy_invoice' => 'required|file|mimes:pdf,xlsx,xls,jpg,jpeg,png|max:5120',
            'internal_memo_entertain' => 'sometimes|file|mimes:pdf,xlsx,xls,jpg,jpeg,png|max:5120',
            'entertain_realization_form' => 'sometimes|file|mimes:pdf,xlsx,xls,jpg,jpeg,png|max:5120',
            'minutes_of_meeting' => 'sometimes|file|mimes:pdf,xlsx,xls,jpg,jpeg,png|max:5120',
            'nominative_summary' => 'sometimes|file|mimes:pdf,xlsx,xls,jpg,jpeg,png|max:5120',
            'cic_form' => 'sometimes|file|mimes:pdf,xlsx,xls,jpg,jpeg,png|max:5120',
            'transfer_evidence' => 'sometimes|file|mimes:pdf,xlsx,xls,jpg,jpeg,png|max:5120',
            'other_document' => 'sometimes|file|mimes:pdf,xlsx,xls,jpg,jpeg,png|max:15360',
        ];
    }
}
