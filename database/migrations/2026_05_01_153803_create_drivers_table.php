<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run migrations
     */
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Linked User Account
            |--------------------------------------------------------------------------
            */

            $table->foreignId('user_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Driver Information
            |--------------------------------------------------------------------------
            */

            $table->string('vehicle_type')
                ->nullable();

            $table->string('license_number')
                ->nullable()
                ->unique();

            /*
            |--------------------------------------------------------------------------
            | Availability
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_available')
                ->default(true);

            /*
            |--------------------------------------------------------------------------
            | Current Location
            |--------------------------------------------------------------------------
            */

            $table->string('current_location')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse migrations
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};