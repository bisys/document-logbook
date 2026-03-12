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
            'car_form' => 'required|file|mimes:pdf|max:500',
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
