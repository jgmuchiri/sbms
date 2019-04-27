<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();//['type':'email']
            $table->string('cell')->nullable();
            $table->string('phone')->nullable();
            $table->string('fax')->nullable();
            $table->string('company')->nullable();
            $table->string('job_title')->nullable();
            $table->string('dept')->nullable();
            $table->string('website')->nullable();
            $table->text('address')->nullable();
            $table->date('dob')->nullable();
            $table->text('notes')->nullable();
            $table->string('photo')->nullable();
            $table->text('social')->nullable();
            $table->string('im')->nullable();
            $table->text('misc')->nullable();
            $table->timestamps();
        });

        Schema::create('contact_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('group_name')->unique();
            $table->string('desc')->nullable();
            $table->timestamps();
        });

        Schema::create('contact_group', function (Blueprint $table) {
            $table->integer('contact_id')->unsigned();
            $table->integer('group_id')->unsigned();
            $table->primary(['contact_id','group_id']);
            $table->foreign('contact_id')->references('id')->on('contacts');
            $table->foreign('group_id')->references('id')->on('contact_groups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('contact_group');
        Schema::drop('contact_groups');
        Schema::drop('contacts');
    }
}
