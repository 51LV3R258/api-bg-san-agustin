<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prices', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained()->onUpdate('cascade');
            $table->foreignId('unit_id')->constrained()->onUpdate('cascade');
            $table->primary(['product_id', 'unit_id']);
            $table->decimal('detalle', 6, 4);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prices');
    }
}
