<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('guid')->unique();
            $table->integer('user_id')->unsigned();
            $table->float('tax')->nullable();
            $table->date('due_date');
            $table->text('notes');
            $table->string('status');
            $table->integer('allow_online_pay')->nullable();
            $table->integer('created_by');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('invoice_id')->unsigned();
            $table->string('itemName');
            $table->string('itemDesc');
            $table->string('itemQty')->nullable();
            $table->string('itemPrice');
            $table->integer('created_by');
            $table->timestamps();

            $table->foreign('invoice_id')->references('id')->on('invoices');

        });

        Schema::create('invoice_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('invoice_id')->unsigned();
            $table->string('txn_id');
            $table->float('txn_amount');
            $table->dateTime('txn_date');
            $table->string('txn_status')->nullable();
            $table->string('txn_tax')->nullable();
            $table->string('pay_method')->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();

            $table->foreign('invoice_id')->references('id')->on('invoices');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoice_payments');
    }
}
