<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketThreadsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ticket_threads', function($table){
                        $table->increments('id');
			$table->integer('ticket_id');
			$table->text('title');
			$table->text('comment');
                        $table->text('follow_up_action')->nullable();
			$table->dateTime('deleted_at')->nullable();
                        $table->boolean('is_private')->default(0);
                        $table->boolean('is_reminder')->default(0);
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
            Schema::drop('ticket_threads');
	}

}
