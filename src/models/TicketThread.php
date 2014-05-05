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
    
    /*
     * Created by: Amit Garg
     * Created on:5-May-2014
     * Description:
     *
     */

    public function commentedBy() {
        return $this->belongsTo('User', 'commented_by_id');
    }
    
}
