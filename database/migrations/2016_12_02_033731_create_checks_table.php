<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('check_no');
            $table->integer('payee_id')->unsigned();
            $table->string('payee_name');
            $table->string('payee_address')->nullable();
            $table->float('amount');
            $table->string('memo')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->nullable(); //draft,sent,cashed,void
            $table->softDeletes();
            $table->integer('created_by')->unsigned();
            $table->timestamps();

            $table->foreign('payee_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('checks');
    }
}
