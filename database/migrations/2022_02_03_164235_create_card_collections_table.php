<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('card_collections', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('collection_id');
            $table->foreign('collection_id')->references('id')->on('Collections');
            $table->unsignedBigInteger('card_id');
            $table->foreign('card_id')->references('id')->on('Cards');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('card_collections');
    }
}
