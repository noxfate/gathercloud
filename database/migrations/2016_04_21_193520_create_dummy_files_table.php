<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDummyFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dummy_files', function (Blueprint $table) {
            $table->increments('id');
            $table->string('path');
            $table->integer('real_store')->unsigned();
            $table->string('dummy_path');
            $table->integer('dummy_store')->unsigned();
            $table->timestamps();
            $table->foreign('real_store')
                ->references('id')->on('tokens')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('dummy_store')
                ->references('id')->on('tokens')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('dummy_files');
    }
}
