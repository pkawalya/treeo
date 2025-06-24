<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodsReceipt extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'grn_number',
        'purchase_order_id',
        'receipt_date',
        'received_by',
        'status',
        'notes',
        'total_quantity',
        'created_by',
    ];

    protected $casts = [
        'receipt_date' => 'date',
        'total_quantity' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->grn_number)) {
                $model->grn_number = 'GRN-' . date('Ymd') . '-' . strtoupper(uniqid());
            }
            if (auth()->check()) {
                $model->created_by = auth()->id();
                $model->received_by = $model->received_by ?? auth()->user()->name;
            }
        });

        static::saved(function ($model) {
            $model->calculateTotalQuantity();
        });
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function items()
    {
        return $this->hasMany(GoodsReceiptItem::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function calculateTotalQuantity()
    {
        $this->total_quantity = $this->items()->sum('quantity_received');
        $this->saveQuietly(); // Use saveQuietly to prevent infinite loop
    }

    public function canBeEdited()
    {
        return in_array($this->status, ['draft', 'received']);
    }

    public function canBeDeleted()
    {
        return $this->status === 'draft';
    }

    public function updateInventory()
    {
        if ($this->status !== 'received') {
            return;
        }

        foreach ($this->items as $item) {
            if ($item->condition !== 'good') {
                continue;
            }

            Inventory::create([
                'seedling_id' => $item->seedling_id,
                'quantity' => $item->quantity_received,
                'batch_number' => $item->batch_number,
                'procurement_date' => $this->receipt_date,
                'expiry_date' => $item->expiry_date,
                'status' => 'in_stock',
                'notes' => 'Received via GRN: ' . $this->grn_number,
            ]);
        }
    }
}
