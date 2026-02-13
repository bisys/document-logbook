<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'approvable_id',
        'approvable_type',
        'approval_role_id',
        'user_id',
        'approval_status_id',
        'approval_at',
        'remark',
    ];

    protected $dates = ['approval_at'];

    public function approvable()
    {
        return $this->morphTo();
    }

    public function role()
    {
        return $this->belongsTo(ApprovalRole::class, 'approval_role_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function status()
    {
        return $this->belongsTo(ApprovalStatus::class, 'approval_status_id');
    }
}
