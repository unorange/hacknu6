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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("client_id");
            $table->unsignedBigInteger("deliveryman_id");
            $table->string("starting_point");
            $table->string("end_point");
            $table->unsignedBigInteger("IIN");
            $table->enum("status",[
                "created",
                "on_delivery",
                "delivery_id"
            ]);

            $table->time("start_time");
            $table->time("end_time")->nullable();
            $table->timestamps();

            $table->foreign("client_id")
                ->references("id")
                ->on("clients")
                ->onUpdate("cascade")
                ->onDelete("cascade");

            $table->foreign("deliveryman_id")
                ->references("id")
                ->on("deliverymans")
                ->onUpdate("cascade")
                ->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
