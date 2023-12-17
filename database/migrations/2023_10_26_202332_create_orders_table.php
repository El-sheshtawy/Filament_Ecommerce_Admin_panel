<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {

            $table->id();

            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();

            $table->string('number')->unique();

            $table->enum('status', ['pending', 'processing', 'completed', 'declined']);

            $table->decimal('shipping_price')->nullable();

            $table->longText('notes');

            $table->softDeletes();

            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
