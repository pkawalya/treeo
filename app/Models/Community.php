<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Community extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'leader_name',
        'contact',
        'district',
        'sub_county',
        'parish',
        'village',
        'latitude',
        'longitude',
        'member_count',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'member_count' => 'integer',
    ];

    public function distributions()
    {
        return $this->morphMany(Distribution::class, 'recipient');
    }
}
