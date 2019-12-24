<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreditCards extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_cards', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('client_id')->unsigned();
            $table->foreign('client_id')->references('id')->on('clients');
            $table->string('name');
            $table->integer('parts_number');
            $table->boolean('imported');
            $table->string('interest');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('extension');
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
        Schema::dropIfExists('credit_cards');
    }
}
