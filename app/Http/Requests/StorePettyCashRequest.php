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
            'pcr_form' => 'required|file|mimes:pdf|max:500',
            'document_number' => 'required|string|max:255|unique:petty_cash,document_number',
            'original_invoice' => 'required|file|mimes:pdf|max:500',
            'copy_invoice' => 'required|file|mimes:pdf|max:500',
            'internal_memo_entertain' => 'sometimes|file|mimes:pdf|max:500',
            'entertain_realization_form' => 'sometimes|file|mimes:pdf|max:500',
            'minutes_of_meeting' => 'sometimes|file|mimes:pdf|max:500',
            'nominative_summary' => 'sometimes|file|mimes:pdf|max:500',
            'cic_form' => 'sometimes|file|mimes:pdf|max:500',
            'budget_plan' => 'required|file|mimes:pdf|max:500',
        ];
    }
}
