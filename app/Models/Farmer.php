<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Farmer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'community_id',
        'name',
        'phone',
        'email',
        'district',
        'sub_county',
        'parish',
        'village',
        'latitude',
        'longitude',
        'status',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function distributions()
    {
        return $this->morphMany(Distribution::class, 'recipient');
    }
    
    public function community()
    {
        return $this->belongsTo(Community::class);
    }
}
