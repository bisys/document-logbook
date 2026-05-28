<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class InternationalTripRealization extends Model
{
    protected $table = 'international_trip_realization';

    protected $fillable = [
        'international_trip_id',
        'number',
        'user_id',
        'cost_center_id',
        'transfer_evidence',
        'other_document',
        'document_status_id',
        'edit_count',
        'is_hardfile_submitted',
        'hardfile_submitted_at',
        'hardfile_received_at',
        'hardfile_received_by',
    ];

    protected $casts = [
        'hardfile_submitted_at' => 'datetime',
        'hardfile_received_at' => 'datetime',
        'is_hardfile_submitted' => 'boolean',
    ];

    public function hardfileReceivedByUser()
    {
        return $this->belongsTo(User::class, 'hardfile_received_by');
    }

    public function trip()
    {
        return $this->belongsTo(InternationalTrip::class, 'international_trip_id');
    }

    /**
     * Get user through the linked International Trip
     */
    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            InternationalTrip::class,
            'id', // Foreign key on international_trip
            'id', // Foreign key on users
            'international_trip_id', // Local key on international_trip_realization
            'user_id' // Local key on international_trip
        );
    }

    /**
     * Get cost center through the linked International Trip
     */
    public function costCenter()
    {
        return $this->hasOneThrough(
            CostCenter::class,
            InternationalTrip::class,
            'id',
            'id',
            'international_trip_id',
            'cost_center_id'
        );
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
        $prefix = 'ITARR';
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
