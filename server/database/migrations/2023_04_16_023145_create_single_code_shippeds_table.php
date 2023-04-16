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
        Schema::create('single_code_shippeds', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("code");
            $table->unsignedBigInteger("delivery_id");
            $table->timestamps();

            $table->foreign("delivery_id")->references("id")->on("deliveries")->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('single_code_shippeds');
    }
};
