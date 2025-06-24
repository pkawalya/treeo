<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Monitoring extends Model
{
    use HasFactory, SoftDeletes;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'monitoring';

    protected $fillable = [
        'distribution_id',
        'growth_stage',
        'observation_date',
        'supervisor_id',
        'notes',
        'image_urls',
        'latitude',
        'longitude',
        'environmental_conditions',
    ];

    protected $casts = [
        'observation_date' => 'date',
        'image_urls' => 'array',
        'environmental_conditions' => 'array',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function distribution()
    {
        return $this->belongsTo(Distribution::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }
}
