<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsReceiptItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'goods_receipt_id',
        'purchase_order_item_id',
        'seedling_id',
        'quantity_ordered',
        'quantity_received',
        'unit_price',
        'total',
        'batch_number',
        'expiry_date',
        'notes',
        'condition',
    ];

    protected $casts = [
        'quantity_ordered' => 'decimal:2',
        'quantity_received' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
        'expiry_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->total = $model->quantity_received * $model->unit_price;
        });

        static::saved(function ($model) {
            $model->goodsReceipt->calculateTotalQuantity();
        });
    }

    public function goodsReceipt()
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }

    public function seedling()
    {
        return $this->belongsTo(Seedling::class);
    }
}
