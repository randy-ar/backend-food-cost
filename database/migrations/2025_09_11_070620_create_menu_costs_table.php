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
        Schema::create('menu_costs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('menu_id')->unsigned();
            $table->integer('hpp_cost')->unsigned()->nullable();
            $table->integer('overhead')->unsigned()->nullable();
            $table->integer('total_cost')->unsigned()->nullable();
            $table->integer('selling_price')->unsigned()->nullable();
            $table->integer('food_cost')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('menu_id')->references('id')->on('menus')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_costs');
    }
};
