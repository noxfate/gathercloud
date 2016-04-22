<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->string('connection_name');
            $table->string('connection_email');
            $table->longText('access_token');
            $table->dateTime('access_token_expired');
            $table->longText('refresh_token');
            $table->dateTime('refresh_token_expired');
            $table->longText('gtc_folder');
            $table->integer('user_id')->unsigned();
            $table->timestamps();

            // Provider supposed to depend on Provider Table
            $table->integer('provider');
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tokens');
    }
}
