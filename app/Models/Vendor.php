<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // Company Information
        'name',
        'company_registration',
        'vendor_type',
        'is_active',
        
        // Contact Information
        'contact_person',
        'contact_position',
        'email',
        'phone',
        'alternative_phone',
        
        // Address Information
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        
        // Banking Information
        'bank_name',
        'bank_branch',
        'bank_account_number',
        'bank_account_name',
        'swift_code',
        'tax_identification_number',
        
        // Payment Terms
        'payment_terms',
        'credit_limit',
        'currency',
        
        // Classification
        'preferred_supplier',
        'vendor_rating',
        'categories',
        
        // Notes & Documents
        'notes',
        'documents',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'credit_limit' => 'decimal:2',
        'categories' => 'array',
        'documents' => 'array',
        'is_preferred' => 'boolean',
    ];

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }
}
