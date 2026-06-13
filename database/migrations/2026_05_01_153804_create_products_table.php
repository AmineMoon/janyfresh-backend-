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
        // Create the table only if it does not already exist
        if (!Schema::hasTable('products')) {

            Schema::create('products', function (Blueprint $table) {

                // Primary key
                $table->id();

                /*
                |--------------------------------------------------------------------------
                | Product Information
                |--------------------------------------------------------------------------
                */

                // Product name
                $table->string('name');

                // Optional product description
                $table->text('description')
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Category Relationships
                |--------------------------------------------------------------------------
                */

                // Main category relationship
                $table->foreignId('category_id')
                    ->constrained('categories')
                    ->cascadeOnDelete();

                // Optional subcategory relationship
                $table->foreignId('subcategory_id')
                    ->nullable()
                    ->constrained('subcategories')
                    ->nullOnDelete();

                /*
                |--------------------------------------------------------------------------
                | Product Unit
                |--------------------------------------------------------------------------
                */

                // Allowed measurement units
                $table->enum('unit', [
                    'kg',
                    'box',
                    'piece'
                ]);

                /*
                |--------------------------------------------------------------------------
                | Pricing & Inventory
                |--------------------------------------------------------------------------
                */

                // Product price
                $table->decimal('price', 10, 2);

                // Available stock quantity
                $table->decimal('quantity', 8, 2)
                    ->default(0);

                /*
                |--------------------------------------------------------------------------
                | Product Status
                |--------------------------------------------------------------------------
                */

                // Product active/inactive status
                $table->boolean('is_active')
                    ->default(true);

                /*
                |--------------------------------------------------------------------------
                | Creator Information
                |--------------------------------------------------------------------------
                */

                // User who created the product
                $table->unsignedBigInteger('created_by')
                    ->index();

                // created_at & updated_at timestamps
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};