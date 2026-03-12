<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCashAdvanceDrawRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cost_center_id' => 'required|exists:cost_centers,id',
            'car_form' => 'sometimes|file|mimes:pdf|max:500',
            'document_number' => 'required|string|max:255|unique:cash_advance_draw,document_number,' . $this->route('cashAdvanceDraw')->id,
            'proposal_or_monitor_budget' => 'sometimes|file|mimes:pdf|max:500',
            'budget_plan' => 'sometimes|file|mimes:pdf|max:500',
        ];
    }
}
