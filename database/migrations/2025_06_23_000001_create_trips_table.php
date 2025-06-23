<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->string('workplace_name');
            $table->string('workplace_address');
            $table->decimal('distance_km', 8, 2);
            $table->date('departure_date');
            $table->date('return_date');
            $table->integer('overnight_days');
            $table->decimal('cost_per_km', 6, 4)->default(0.30);
            $table->decimal('total_cost', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};