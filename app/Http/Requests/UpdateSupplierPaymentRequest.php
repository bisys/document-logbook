<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupplierPaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'cost_center_id' => 'required|exists:cost_centers,id',
            'spr_form' => 'sometimes|file|mimes:pdf|max:500',
            'document_number' => 'required|string|max:255|unique:supplier_payment,document_number,' . $this->route('supplierPayment')->id,
            'original_invoice' => 'sometimes|file|mimes:pdf|max:500',
            'copy_invoice' => 'sometimes|file|mimes:pdf|max:500',
            'tax_invoice' => 'sometimes|file|mimes:pdf|max:500',
            'agreement' => 'sometimes|file|mimes:pdf|max:500',
            'internal_memo_entertain' => 'sometimes|file|mimes:pdf|max:500',
            'entertain_realization_form' => 'sometimes|file|mimes:pdf|max:500',
            'minutes_of_meeting' => 'sometimes|file|mimes:pdf|max:500',
            'nominative_summary' => 'sometimes|file|mimes:pdf|max:500',
            'calculation_summary' => 'sometimes|file|mimes:pdf|max:500',
            'budget_plan' => 'sometimes|file|mimes:pdf|max:500',
        ];
    }
}
