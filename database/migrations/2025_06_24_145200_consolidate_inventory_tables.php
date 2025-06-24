<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Drop the old tables if they exist
        Schema::dropIfExists('inventory');
        Schema::dropIfExists('inventories');

        // Create the new consolidated inventory table
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seedling_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('vendors')->onDelete('set null');
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            
            $table->string('sku')->unique()->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('quantity', 10, 2)->default(0);
            $table->string('unit_of_measure')->default('pcs');
            $table->decimal('unit_cost', 10, 2)->default(0);
            $table->decimal('reorder_level', 10, 2)->default(0);
            $table->string('location')->nullable();
            $table->string('batch_number')->nullable();
            $table->date('procurement_date');
            $table->date('expiry_date')->nullable();
            $table->enum('status', ['in_stock', 'reserved', 'distributed'])->default('in_stock');
            
            $table->timestamp('last_stocked_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('name');
            $table->index('sku');
            $table->index('category_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventories');
    }
};
