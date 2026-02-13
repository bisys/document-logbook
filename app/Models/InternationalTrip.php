<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InternationalTrip extends Model
{
    protected $table = 'international_trip';

    protected $fillable = [
        'number',
        'user_id',
        'cost_center_id',
        'itar_form',
        'document_number',
        'internal_memo',
        'summary_bussiness_trip',
        'overseas_allowance_form',
        'bussiness_trip_allowance',
        'rate',
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
