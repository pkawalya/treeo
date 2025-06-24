<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            // Company Information
            $table->string('company_registration')->nullable()->after('name');
            $table->string('vendor_type')->default('supplier')->after('company_registration');
            
            // Contact Information
            $table->string('contact_position')->nullable()->after('contact_person');
            $table->string('alternative_phone')->nullable()->after('phone');
            
            // Address Information
            $table->string('city')->nullable()->after('address');
            $table->string('state')->nullable()->after('city');
            $table->string('country')->default('Uganda')->after('state');
            $table->string('postal_code')->nullable()->after('country');
            
            // Banking Information
            $table->string('bank_branch')->nullable()->after('bank_name');
            $table->string('swift_code')->nullable()->after('bank_account_name');
            
            // Payment Terms
            $table->string('payment_terms')->default('net30')->after('swift_code');
            $table->decimal('credit_limit', 15, 2)->nullable()->after('payment_terms');
            $table->string('currency', 3)->default('UGX')->after('credit_limit');
            
            // Classification
            $table->string('preferred_supplier')->default('no')->after('currency');
            $table->string('vendor_rating', 1)->nullable()->after('preferred_supplier');
            $table->json('categories')->nullable()->after('vendor_rating');
            
            // Documents
            $table->json('documents')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn([
                'company_registration',
                'vendor_type',
                'contact_position',
                'alternative_phone',
                'city',
                'state',
                'country',
                'postal_code',
                'bank_branch',
                'swift_code',
                'payment_terms',
                'credit_limit',
                'currency',
                'preferred_supplier',
                'vendor_rating',
                'categories',
                'documents'
            ]);
        });
    }
};
