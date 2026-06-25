<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierPaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled in the controller or middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // 'number', 'user_id', and 'document_status_id' are set in the controller, not from user input
            'cost_center_id' => 'required|exists:cost_centers,id',
            'spr_form' => 'required|file|mimes:pdf,xls,xlsx,jpg,jpeg,png|max:5120',
            'document_number' => 'required|string|max:255|unique:supplier_payment,document_number',
            'purpose' => 'required|string|max:255',
            'original_invoice' => 'required|file|mimes:pdf,xls,xlsx,jpg,jpeg,png|max:5120',
            'copy_invoice' => 'required|file|mimes:pdf,xls,xlsx,jpg,jpeg,png|max:5120',
            'tax_invoice' => 'sometimes|file|mimes:pdf,xls,xlsx,jpg,jpeg,png|max:5120',
            'agreement' => 'sometimes|file|mimes:pdf,xls,xlsx,jpg,jpeg,png|max:15360',
            'internal_memo_entertain' => 'sometimes|file|mimes:pdf,xls,xlsx,jpg,jpeg,png|max:5120',
            'entertain_realization_form' => 'sometimes|file|mimes:pdf,xls,xlsx,jpg,jpeg,png|max:5120',
            'minutes_of_meeting' => 'sometimes|file|mimes:pdf,xls,xlsx,jpg,jpeg,png|max:5120',
            'nominative_summary' => 'sometimes|file|mimes:pdf,xls,xlsx,jpg,jpeg,png|max:5120',
            'calculation_summary' => 'sometimes|file|mimes:pdf,xls,xlsx,jpg,jpeg,png|max:5120',
            'budget_plan' => 'required|file|mimes:pdf,xls,xlsx,jpg,jpeg,png|max:5120',
            'other_document' => 'sometimes|file|mimes:pdf,xls,xlsx,jpg,jpeg,png|max:15360',
        ];
    }
}
