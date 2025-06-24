<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Distribution extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'recipient_type',
        'recipient_id',
        'seedling_id',
        'quantity',
        'cost',
        'distribution_date',
        'distributor_id',
        'notes',
        'status',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'cost' => 'decimal:2',
        'distribution_date' => 'date',
    ];

    protected static function booted()
    {
        static::saving(function ($distribution) {
            if ($distribution->seedling) {
                $distribution->cost = $distribution->quantity * $distribution->seedling->unit_cost;
            }
        });
    }

    public function recipient()
    {
        return $this->morphTo();
    }

    public function seedling()
    {
        return $this->belongsTo(Seedling::class);
    }

    public function distributor()
    {
        return $this->belongsTo(User::class, 'distributor_id');
    }

    public function monitoring()
    {
        return $this->hasMany(Monitoring::class);
    }
}
