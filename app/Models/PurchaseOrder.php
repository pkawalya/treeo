<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'po_number',
        'vendor_id',
        'order_date',
        'expected_delivery_date',
        'status',
        'delivery_terms',
        'payment_terms',
        'notes',
        'subtotal',
        'tax',
        'shipping',
        'total',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
        'approved_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'shipping' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->po_number)) {
                $model->po_number = 'PO-' . date('Ymd') . '-' . strtoupper(uniqid());
            }
            if (auth()->check()) {
                $model->created_by = auth()->id();
            }
        });
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function receipts()
    {
        return $this->hasMany(GoodsReceipt::class);
    }

    public function canBeEdited()
    {
        return in_array($this->status, ['draft', 'sent']);
    }

    public function canBeDeleted()
    {
        return $this->status === 'draft';
    }

    public function canBeApproved()
    {
        return $this->status === 'sent' && !$this->approved_at;
    }

    public function calculateTotals()
    {
        $subtotal = $this->items->sum('total');
        $this->subtotal = $subtotal;
        $this->total = $subtotal + $this->tax + $this->shipping;
        $this->save();
    }
}
