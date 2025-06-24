<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Seedling extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'description',
        'growth_stages',
        'image_url',
    ];

    protected $casts = [
        'growth_stages' => 'array',
    ];

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    public function distributions()
    {
        return $this->hasMany(Distribution::class);
    }
}
