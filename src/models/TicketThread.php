<?php

namespace Efusionsoft\Ticket\Models;

class TicketThread extends \Eloquent {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'ticket_threads';
    protected $guarded = array();
    
    /*
     * Created by: Amit Garg
     * Created on:9-April-2014
     * Description:
     *
     */

    public function Ticket() {
         return $this->belongsTo('Ticket', 'ticket_id');
    }
    
}
