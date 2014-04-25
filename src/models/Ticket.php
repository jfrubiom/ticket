<?php

namespace Efusionsoft\Ticket\Models;

class Ticket extends \Eloquent {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tickets';
    protected $guarded = array();
    protected $softDelete = true;

    /**
     * The database table relation.
     *
     * @var string
     */
    public function threads() {
        return $this->hasMany('\Efusionsoft\Ticket\Models\TicketThread');
    }

    public function status() {
        return $this->hasMany('\Efusionsoft\Ticket\Models\TicketStatusHistory');
    }

    public function assigned() {
        return $this->hasMany('\Efusionsoft\Ticket\Models\TicketAssignmentHistory');
    }

    public function criticallity() {
        return $this->hasMany('\Efusionsoft\Ticket\Models\TicketCriticallityHistory');
    }

    public function getTicketList() {
        /*
        $tickets = Ticket::with(
                        array('threads' => function($query) {
                        $query->oldest();
                    },
                            'status' => function($query) {
                        $query->latest();
                    },
                            'assigned' => function($query) {
                        $query->latest();
                    },
                            'criticallity' => function($query) {
                        $query->latest();
                    }
                ))->paginate(20);
         * 
         */
                $tickets = Ticket::paginate(20);
        //print_r($tickets);die;
        // $queries = \DB::getQueryLog();
        // $last_query = end($queries);
        // echo "<pre>";
        // print_r( $queries );
        // print_r($tickets);
        // exit;
       /*
                $data = array();
        $count = 0;
        foreach ($tickets as $ticket) {

            $data[$count]['id'] = $ticket->id;
            $data[$count]['date'] = date('d/m/Y H:i A', strtotime($ticket->ticket_date));
            $user = \User::find($ticket->raised_by_id);
            $data[$count]['from'] = $user->first_name . " " . $user->last_name;
            $data[$count]['title']= $ticket->title;
            if ($ticket->threads) {
                $thread = $ticket->threads->toArray();
               
            }

            if ($ticket->status) {
                $status = $ticket->status->toArray();
                if (!empty($status[count($status) - 1]['status'])) {
                    $data[$count]['status'] = $status[count($status) - 1]['status'];
                }
            }

            if ($ticket->criticallity) {
                $criticallity = $ticket->criticallity->toArray();
                if (!empty($criticallity[count($criticallity) - 1]['criticallity'])) {
                    $data[$count]['criticallity'] = $criticallity[count($criticallity) - 1]['criticallity'];
                }
            }

            if ($ticket->assigned) {
                $assigned = $ticket->assigned->toArray();
                if (!empty($assigned[count($assigned) - 1]['assigned_to_id'])) {
                    $user_id = $assigned[count($assigned) - 1]['assigned_to_id'];
                    $user = \User::find($user_id);
                    $data[$count]['assigned_user'] = $user->first_name . " " . $user->last_name;
                }
            }
            $count++;
        }
        return $data;
        * 
        */
       return $tickets;
    }

    /**
     * Method to check passed id exist in table or not
     * @id integer
     * @return : boolean
     */
    public function exists($id) {
        $ticket = Ticket::find($id);
        if ($ticket->id) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method to check passed id exist in table or not
     * @id integer
     * @return : boolean
     */
    public function getTicket($id) {
        $ticket = Ticket::with(array('threads', 'status', 'assigned', 'criticallity')
                )->where('id', '=', $id)->get()->toArray();
        return $ticket;
    }
    /*
     * 
    */
    public function generator() {
        return $this->belongsTo('User', 'generated_by_id');
    }

    public function raisedBy() {
        return $this->belongsTo('User', 'raised_by_id');
    }
    
    public function raisedFor() {
        return $this->belongsTo('User', 'raised_for_id');
    }
    

}
