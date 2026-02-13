<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class ApprovalRole extends Model
{
    use HasSlug;

    protected $fillable = [
        'name',
        'slug',
        'sequence',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class);
    }
}
