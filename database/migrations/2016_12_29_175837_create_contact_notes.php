<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactNotes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_notes_cats', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cat_name')->unique();
        });
        Schema::create('contact_notes', function (Blueprint $table) {
            $table->integer('contact_id')->unsigned();
            $table->longText('notes');
            $table->integer('contact_notes_cat')->unsigned();
            $table->foreign('contact_notes_cat')->references('id')->on('contact_notes_cats');
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
        Schema::drop('contact_notes');
        Schema::drop('contact_notes_cats');
    }
}
