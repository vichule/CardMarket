<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardSellTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('card_sell', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('card_id');
            $table->foreign('card_id')->references('id')->on('card_collections');
            $table->unsignedBigInteger('user');
            $table->foreign('User')->references('id')->on('users');
            $table->string('Amount'); 
            $table->string('Price');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('card_sell');
    }
}
