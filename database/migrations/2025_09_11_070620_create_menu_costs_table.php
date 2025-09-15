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
            $table->float('hpp_cost')->unsigned()->nullable();
            $table->float('overhead')->unsigned()->nullable();
            $table->float('total_cost')->unsigned()->nullable();
            $table->float('selling_price')->unsigned()->nullable();
            $table->float('food_cost')->unsigned()->nullable();
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
