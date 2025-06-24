<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'seedling_id',
        'quantity',
        'unit',
        'unit_price',
        'total',
        'description',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->total = $model->quantity * $model->unit_price;
        });

        static::saved(function ($model) {
            $model->purchaseOrder->calculateTotals();
        });

        static::deleted(function ($model) {
            $model->purchaseOrder->calculateTotals();
        });
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function seedling()
    {
        return $this->belongsTo(Seedling::class);
    }
}
