<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('name');
            $table->float('amount');
            $table->integer('category')->default(1);
            $table->text('notes')->nullable();
            $table->unsignedInteger('client');
            $table->timestamps();

            $table->foreign('client')->references('id')->on('users');
        });
        Schema::create('expense_cats', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cat_name')->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('expenses');
        Schema::drop('expense_cats');
    }
}
