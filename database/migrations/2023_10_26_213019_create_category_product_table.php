<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('category_product', function (Blueprint $table) {

            $table->foreignId('category_id')->constrained()->cascadeOnDelete();

            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('category_product');
    }
};
