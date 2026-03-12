<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCashAdvanceDrawRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cost_center_id' => 'required|exists:cost_centers,id',
            'car_form' => 'required|file|mimes:pdf|max:500',
            'document_number' => 'required|string|max:255|unique:cash_advance_draw,document_number',
            'proposal_or_monitor_budget' => 'required|file|mimes:pdf|max:500',
            'budget_plan' => 'required|file|mimes:pdf|max:500',
        ];
    }
}
