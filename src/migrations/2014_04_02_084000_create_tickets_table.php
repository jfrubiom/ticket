<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tickets', function($table){
                        $table->increments('id');
			$table->integer('generated_by_id');
			$table->integer('raised_by_id');
			$table->integer('raised_for_id');
                        $table->dateTime('ticket_date');
			$table->dateTime('deleted_at')->nullable();
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
            Schema::drop('tickets');
	}

}
