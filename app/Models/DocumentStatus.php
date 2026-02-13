<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class DocumentStatus extends Model
{
    use HasSlug;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'status',
        'slug',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('status')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function supplierPayments()
    {
        return $this->hasMany(SupplierPayment::class);
    }

    public function pettyCashes()
    {
        return $this->hasMany(PettyCash::class);
    }

    public function cashAdvanceDraws()
    {
        return $this->hasMany(CashAdvanceDraw::class);
    }

    public function cashAdvanceRealizations()
    {
        return $this->hasMany(CashAdvanceRealization::class);
    }

    public function internationalTrips()
    {
        return $this->hasMany(InternationalTrip::class);
    }
}
