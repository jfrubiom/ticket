<?php

namespace Efusionsoft\Ticket\Models;

class TicketCriticallityHistory extends \Eloquent {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'ticket_criticallity_histories';
    protected $guarded = array();
    /*
     * Created by: Amit Garg
     * Created on:date
     * Description:
     *
     */
    public function getValue(){
        return $this->criticallity;
    }




    public function ticketId(){
        $this->belongsTo('Ticket','ticket_id');
    }
}
