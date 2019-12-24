<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Clients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('file_id')->unsigned();
            $table->foreign('file_id')->references('id')->on('imported_files');
            $table->integer('key');
            $table->string('name');
            $table->string('account');
            $table->string('email');
            $table->string('address');
            $table->boolean('checked');
            $table->text('description');
            $table->string('interest');
            $table->date('date_of_birth');
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
        Schema::dropIfExists('clients');   
    }
}
