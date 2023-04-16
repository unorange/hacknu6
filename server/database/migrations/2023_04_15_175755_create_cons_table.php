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
        Schema::create('cons', function (Blueprint $table){
            $table->id();
            $table->float("long");
            $table->float("lat");
            $table->string("address");
            $table->unsignedBigInteger("region_id");
            $table->timestamps();

            $table->foreign("region_id")->references("id")->on("regions")->onUpdate("cascade")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cons');
    }
};
