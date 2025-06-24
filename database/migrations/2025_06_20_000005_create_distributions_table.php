<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('distributions', function (Blueprint $table) {
            $table->id();
            $table->enum('recipient_type', ['farmer', 'community']);
            $table->unsignedBigInteger('recipient_id');
            $table->foreignId('seedling_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity');
            $table->date('distribution_date');
            $table->foreignId('distributor_id')->constrained('users')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('distributions');
    }
};
