<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('currency_id');
            $table->decimal('price', 15, 2);
            $table->timestamps();

            $table->foreign('product_id')
                ->references('id')->on('products')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('currency_id')
                ->references('id')->on('currencies')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_prices');
    }
};