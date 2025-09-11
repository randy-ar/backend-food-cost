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
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('unit_id')->unsigned();
            $table->integer('weight')->unsigned();
            $table->integer('price')->unsigned();
            $table->integer('pieces')->unsigned()->nullable();
            $table->integer('price_per_gr')->unsigned()->nullable();
            $table->integer('price_per_pcs')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
