<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Revision extends Model
{
    protected $fillable = [
        'revisable_id',
        'revisable_type',
        'revision_times',
        'user_id',
        'revision_status_id',
        'revision_at',
        'remark'
    ];

    protected $dates = ['revision_at'];

    public function revisable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function status()
    {
        return $this->belongsTo(RevisionStatus::class, 'revision_status_id');
    }
}
