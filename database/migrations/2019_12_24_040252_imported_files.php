<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ImportedFiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {      
        Schema::create('imported_files', function (Blueprint $table) {
            $table->bigIncrements('id');
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
        Schema::dropIfExists('imported_files');
    }
}
