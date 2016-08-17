<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
            $table->text('content');
            $table->timestamps();
        });

        App\Task::create([
            'name' => '任务1',
            'content' => '完成crud'
        ]);
        App\Task::create([
            'name' => '任务1',
            'content' => '完成教程'
        ]);
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tasks');
    }
}
