<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePettyCashRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cost_center_id' => 'required|exists:cost_centers,id',
            'pcr_form' => 'required|file|mimes:pdf,xlsx,xls,jpg,jpeg,png|max:5120',
            'document_number' => 'required|string|max:255|unique:petty_cash,document_number',
            'purpose' => 'required|string|max:255',
            'original_invoice' => 'required|file|mimes:pdf,xlsx,xls,jpg,jpeg,png|max:5120',
            'copy_invoice' => 'required|file|mimes:pdf,xlsx,xls,jpg,jpeg,png|max:5120',
            'tax_invoice' => 'sometimes|file|mimes:pdf,xlsx,xls,jpg,jpeg,png|max:5120',
            'internal_memo_entertain' => 'sometimes|file|mimes:pdf,xlsx,xls,jpg,jpeg,png|max:5120',
            'entertain_realization_form' => 'sometimes|file|mimes:pdf,xlsx,xls,jpg,jpeg,png|max:5120',
            'minutes_of_meeting' => 'sometimes|file|mimes:pdf,xlsx,xls,jpg,jpeg,png|max:5120',
            'nominative_summary' => 'sometimes|file|mimes:pdf,xlsx,xls,jpg,jpeg,png|max:5120',
            'cic_form' => 'sometimes|file|mimes:pdf,xlsx,xls,jpg,jpeg,png|max:5120',
            'budget_plan' => 'required|file|mimes:pdf,xlsx,xls,jpg,jpeg,png|max:5120',
            'other_document' => 'sometimes|file|mimes:pdf,xlsx,xls,jpg,jpeg,png|max:15360',
        ];
    }
}
