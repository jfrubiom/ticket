<?php
namespace Efusionsoft\Ticket\Models;
class TicketStatusHistory extends \Eloquent  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'ticket_status_histories';
        protected $guarded = array();
        public function ticket() {
		return $this->belongsTo('\Efusionsoft\Ticket\Models\Ticket');
	}
}