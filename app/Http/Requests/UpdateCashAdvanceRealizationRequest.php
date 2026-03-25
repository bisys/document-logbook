<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCashAdvanceRealizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'car_form' => 'sometimes|file|mimes:pdf|max:500',
            'original_invoice' => 'sometimes|file|mimes:pdf|max:500',
            'copy_invoice' => 'sometimes|file|mimes:pdf|max:500',
            'internal_memo_entertain' => 'sometimes|file|mimes:pdf|max:500',
            'entertain_realization_form' => 'sometimes|file|mimes:pdf|max:500',
            'minutes_of_meeting' => 'sometimes|file|mimes:pdf|max:500',
            'nominative_summary' => 'sometimes|file|mimes:pdf|max:500',
            'cic_form' => 'sometimes|file|mimes:pdf|max:500',
        ];
    }
}
