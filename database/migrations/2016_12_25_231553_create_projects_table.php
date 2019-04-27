<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->integer('client')->nullable()->unsigned();
            $table->foreign('client')->references('id')->on('users');
            $table->date('p_start');
            $table->date('p_end')->nullable();
            $table->text('details')->nullable();
            $table->integer('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users');
            $table->string('p_status')->nullable();
            $table->timestamps();
        });

        Schema::create('project_milestones',function(Blueprint $table){
            $table->increments('id');
            $table->integer('project_id')->unsigned();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->string('name');
            $table->date('m_start');
            $table->date('m_end')->nullable();
            $table->string('m_status')->nullable();
            $table->integer('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users');
            $table->timestamps();
        });

        Schema::create('project_tasks',function(Blueprint $table){
            $table->increments('id');
            $table->integer('project_id')->unsigned();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->integer('milestone_id')->unsigned();
            $table->foreign('milestone_id')->references('id')->on('project_milestones')->onDelete('cascade');
            $table->string('task_name');
            $table->text('desc')->nullable();
            $table->integer('assigned_to')->unsigned()->nullable();
            $table->foreign('assigned_to')->references('id')->on('users');
            $table->date('t_start');
            $table->date('t_end');
            $table->string('t_status')->nullable();
            $table->timestamps();
        });

        Schema::create('project_files',function(Blueprint $table) {
            $table->increments('id');
            $table->integer('project_id')->unsigned();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->string('filename');
            $table->string('path');
            $table->string('desc')->nullable();
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('size')->nullable();
            $table->timestamps();
        });

        Schema::create('project_members',function(Blueprint $table) {
            $table->increments('id');
            $table->integer('project_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->timestamps();
            $table->index(['project_id','user_id']);
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });

        Schema::create('project_messages',function(Blueprint $table) {
            $table->increments('id');
            $table->integer('project_id')->unsigned();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('parent_id')->unsigned()->nullable();
            $table->foreign('parent_id')->references('id')->on('project_messages')->onDelete('cascade');
            $table->text('message');
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
        Schema::dropIfExists('project_messages');
        Schema::dropIfExists('project_members');
        Schema::dropIfExists('files');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('milestones');
        Schema::dropIfExists('projects');
    }
}
