<?php

/*
 * Created on: 05-May-2014 to create event logs
 */

namespace Efusionsoft\Ticket\Models;

/**
 * Description of TicketEventLog
 *
 * @author EFS057
 */
class TicketEventLog extends \Eloquent {
    protected $table = 'ticket_event_logs';
    protected $guarded = array('id');
    
    /*
     * Created by: Amit Garg
     * Created on:06-May-2014
     * Description:
     *
     */

     public function createdBy() {
        return $this->belongsTo('User', 'created_by_id');
    }
}
