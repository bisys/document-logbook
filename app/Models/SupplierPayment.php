<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierPayment extends Model
{
    protected $table = 'supplier_payment';

    protected $fillable = [
        'number',
        'user_id',
        'cost_center_id',
        'spr_form',
        'document_number',
        'original_invoice',
        'copy_invoice',
        'tax_invoice',
        'agreement',
        'internal_memo_entertain',
        'entertain_realization_form',
        'minutes_of_meeting',
        'nominative_summary',
        'calculation_summary',
        'budget_plan',
        'document_status_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class);
    }

    public function status()
    {
        return $this->belongsTo(DocumentStatus::class, 'document_status_id');
    }

    public function approvals()
    {
        return $this->morphMany(Approval::class, 'approvable');
    }

    public function revisions()
    {
        return $this->morphMany(Revision::class, 'revisable');
    }
}
