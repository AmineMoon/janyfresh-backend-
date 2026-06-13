<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {

    $table->id();
     
    $table->foreignId('order_id')
        ->unique()
        ->constrained()
        ->cascadeOnDelete();

    $table->foreignId('driver_id')
    ->nullable()
    ->constrained('drivers')
    ->nullOnDelete();

    $table->foreignId('assigned_by')
        ->nullable()
        ->constrained('users')
        ->cascadeOnDelete();

    $table->enum('status', [
        'assigned',
        'picked_up',
        'in_transit',
        'delivered',
        'failed',
        'cancelled'
    ])->default('assigned');

    $table->timestamp('picked_up_at')->nullable();

    $table->timestamp('in_transit_at')->nullable();

    $table->timestamp('delivered_at')->nullable();

    $table->text('notes')->nullable();

     $table->decimal('driver_earnings', 10, 2)->default(0);

        

    $table->timestamps();

    $table->index(['driver_id', 'status']);
});
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};