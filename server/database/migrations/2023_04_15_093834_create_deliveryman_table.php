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
        Schema::create('deliverymans', function (Blueprint $table) {
            $table->id();
            $table->string("first_name");
            $table->string("last_name");
            $table->string("phone",11);
            $table->enum("status",[
                "inactive",
                "active",
                "in_order"
            ]);
            $table->unsignedBigInteger("delivery_service_id");
            $table->timestamps();

            $table->foreign("delivery_service_id")->
                references("id")->
                on("delivery_services")->
                onUpdate("cascade")->
                onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivermen');
    }
};
