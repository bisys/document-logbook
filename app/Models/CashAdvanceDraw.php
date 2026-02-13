<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashAdvanceDraw extends Model
{
    protected $table = 'cash_advance_draw';

    protected $fillable = [
        'number',
        'user_id',
        'cost_center_id',
        'car_form',
        'document_number',
        'proposal_or_monitor_budget',
        'budget_plan',
        'document_status_id'
    ];

    public function realization()
    {
        return $this->hasOne(CashAdvanceRealization::class, 'cash_advance_draw_id');
    }

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
