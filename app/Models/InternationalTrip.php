<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

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
        'document_status_id',
        'edit_count'
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

    public static function generateNumber()
    {
        $prefix = 'ITAR';
        $today = Carbon::now()->format('dmY');

        $last = self::whereDate('created_at', Carbon::today())
            ->where('number', 'like', $prefix . $today . '%')
            ->orderByDesc('id')
            ->first();

        if ($last) {
            $lastNumber = (int) substr($last->number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $today . $newNumber;
    }

    public function generateEditedFileName($fieldName, $originalPath)
    {
        $editCount = ($this->edit_count ?? 0) + 1;
        $extension = pathinfo($originalPath, PATHINFO_EXTENSION);
        return "{$fieldName}_{$this->number}_edited({$editCount}).{$extension}";
    }
}
