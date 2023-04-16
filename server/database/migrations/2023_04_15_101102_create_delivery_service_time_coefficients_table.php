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
        Schema::create('delivery_service_time_coefficients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("delivery_service_id");
            $table->time("start_time");
            $table->time("end_time");
            $table->float("coefficient");
            $table->timestamps();

            $table->foreign("delivery_service_id")
                ->references("id")
                ->on("delivery_services")
                ->onUpdate("cascade")
                ->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_service_time_coefficients');
    }
};
