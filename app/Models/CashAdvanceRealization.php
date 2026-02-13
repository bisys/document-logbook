<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashAdvanceRealization extends Model
{
    protected $table = 'cash_advance_realization';

    protected $fillable = [
        'cash_advance_draw_id',
        'number',
        'car_form',
        'document_number',
        'original_invoice',
        'copy_invoice',
        'internal_memo_entertain',
        'entertain_realization_form',
        'minutes_of_meeting',
        'nominative_summary',
        'cic_form',
        'budget_plan',
        'document_status_id'
    ];

    public function draw()
    {
        return $this->belongsTo(CashAdvanceDraw::class, 'cash_advance_draw_id');
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
