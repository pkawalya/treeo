<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    use SoftDeletes;

    protected $table = 'inventories';

    protected $fillable = [
        'seedling_id',
        'supplier_id',
        'category_id',
        'sku',
        'name',
        'description',
        'quantity',
        'unit_of_measure',
        'unit_cost',
        'reorder_level',
        'location',
        'batch_number',
        'procurement_date',
        'expiry_date',
        'status',
        'last_stocked_at',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'reorder_level' => 'decimal:2',
        'procurement_date' => 'date',
        'expiry_date' => 'date',
        'last_stocked_at' => 'datetime',
    ];

    public function seedling(): BelongsTo
    {
        return $this->belongsTo(Seedling::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'supplier_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
