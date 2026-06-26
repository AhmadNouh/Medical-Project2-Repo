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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique(); // form of product name where I can use in the url in place of product id
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2);
            $table->string('sku')->unique(); // Product Code
            $table->integer('stock')->default(0); // quantity in the stock
            $table->string('status')->default('active'); 
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            
            $table->index('status'); // query to get active prodcuts only
            $table->index(['name', 'price']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
