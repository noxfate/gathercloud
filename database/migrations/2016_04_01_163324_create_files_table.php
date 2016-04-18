<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration {

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('files', function(Blueprint $table) {
      // These columns are needed for Baum's Nested Set implementation to work.
      // Column names may be changed, but they *must* all exist and be modified
      // in the model.
      // Take a look at the model scaffold comments for details.
      // We add indexes on parent_id, lft, rgt columns by default.
      $table->increments('id');
      $table->integer('parent_id')->nullable()->index();
      $table->integer('lft')->nullable()->index();
      $table->integer('rgt')->nullable()->index();
      $table->integer('depth')->nullable();



      // Add needed columns here (f.ex: name, slug, path, etc.)
      // $table->string('name', 255);
      $table->string('name');
      $table->string('path');
      $table->integer('bytes');
      $table->string('size');
      $table->string('mime_type')->nullable();
      $table->string('is_dir');
      $table->string('shared');
      $table->string('modified');
      $table->string('conName');
      $table->integer('token_id')->unsigned();
      $table->timestamps();

      $table->foreign('token_id')
          ->references('id')->on('tokens')
          ->onDelete('cascade');


    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::drop('files');
  }

}
