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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('image_path')->nullable();
            $table->decimal('price', 8, 2);
            $table->integer('stock_quantity');
            $table->boolean('is_active')->default(true);
            $table->dateTime('available_from')->nullable();
            $table->dateTime('available_to')->nullable();
            $table->string('sku')->unique();
            $table->text('notes')->nullable();
            $table->integer('weight')->nullable();
            $table->string('dimensions')->nullable();
            $table->string('location')->nullable();
            $table->string('barcode')->unique()->nullable();
            $table->string('serial_number')->unique()->nullable();
            $table->date('manufacture_date')->nullable();
            $table->integer("designer_id")->foreign('designer_id')->references('id')->on('designers')->onDelete('cascade');
            $table->integer("category_id")->foreign('category_id')->references('id')->on('item_categories')->onDelete('cascade');
            $table->integer("material_id")->foreign('material_id')->references('id')->on('materials')->onDelete('cascade');
            $table->integer("supplier_id")->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->integer("color_id")->foreign('color_id')->references('id')->on('colors')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
