<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class CashAdvanceRealization extends Model
{
    protected $table = 'cash_advance_realization';

    protected $fillable = [
        'cash_advance_draw_id',
        'number',
        'user_id',
        'cost_center_id',
        'car_form',
        'original_invoice',
        'copy_invoice',
        'internal_memo_entertain',
        'entertain_realization_form',
        'minutes_of_meeting',
        'nominative_summary',
        'cic_form',
        'transfer_evidence',
        'other_document',
        'document_status_id',
        'edit_count',
        'hardfile_received_at',
        'hardfile_received_by',
    ];

    protected $casts = [
        'hardfile_received_at' => 'datetime',
    ];

    public function hardfileReceivedByUser()
    {
        return $this->belongsTo(User::class, 'hardfile_received_by');
    }

    public function draw()
    {
        return $this->belongsTo(CashAdvanceDraw::class, 'cash_advance_draw_id');
    }

    /**
     * Get user through the linked Cash Advance Draw
     */
    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            CashAdvanceDraw::class,
            'id', // Foreign key on cash_advance_draw
            'id', // Foreign key on users
            'cash_advance_draw_id', // Local key on cash_advance_realization
            'user_id' // Local key on cash_advance_draw
        );
    }

    /**
     * Get cost center through the linked Cash Advance Draw
     */
    public function costCenter()
    {
        return $this->hasOneThrough(
            CostCenter::class,
            CashAdvanceDraw::class,
            'id',
            'id',
            'cash_advance_draw_id',
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
        $prefix = 'CARR';
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
