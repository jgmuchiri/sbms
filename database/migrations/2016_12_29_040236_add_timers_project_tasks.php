<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimersProjectTasks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_tasks', function (Blueprint $table) {
            $table->string('est_hours')->nullable();
            $table->string('actual_hours')->nullable();
            $table->float('est_cost')->nullable();
            $table->float('actual_cost')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_tasks', function (Blueprint $table) {
            $table->dropColumn(['est_hours', 'actual_hours', 'est_cost', 'actual_cost']);
        });
    }
}
