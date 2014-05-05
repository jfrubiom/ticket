<?php

namespace Efusionsoft\Ticket\Models;

use DB;
use Mail;
use Sentry;

class Ticket extends \Eloquent {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tickets';
    protected $guarded = array('id');
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
        $tickets = Ticket::orderBy('id', 'DESC')->paginate(20);
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

    /*
     * Created by: Amit Garg
     * Created on:02-May-2014
     * Description:
     *
     */

    public function getLatestTicketList($cnt = 5) {
        $tickets = Ticket::orderBy('id', 'DESC')->take($cnt)->get();
        return $tickets;
    }

    /*
     * Created by: Amit Garg
     * Created on:03-May-2014
     * Description:To get count of ticket assigned to person
     *
     */

    public function getTicketCountByAssignee($id) {

        return Ticket::where('assigned_to_id', $id)->count();
    }

    /*
     * Created by: Amit Garg
     * Created on:03-May-2014
     * Description: Ticket count by complainer id
     *
     */

    public function getTicketCountByComplainedById($id) {
        return Ticket::where('raised_by_id', $id)->count();
    }

    /*
     * Created by: Amit Garg
     * Created on:03-May-2014
     * Description:
     *
     */

    public function getTicketCountComplainedAgainstById($id) {
        return Ticket::where('raised_for_id', $id)->count();
    }

    /*
     * Created by: Amit Garg
     * Created on:03-May-2014
     * Description:Get ticket count by status
     *
     */

    public function getTicketCountByStatus($st) {
        $results = DB::select(DB::raw("SELECT count(st.ticket_id) as cnt FROM `ticket_status_histories` as st
                join 
               (
               select ticket_id,max(`created_at`) as created_at1 from ticket_status_histories
                   group by ticket_id
               ) as st2 
               on st.ticket_id=st2.ticket_id and st.created_at=st2.created_at1 where st.status=:status"), array(
                    'status' => $st,
        ));
        //print_r($results);die;
        return $results[0]->cnt;
    }

    /*
     * Created by: Amit Garg
     * Created on:05-May-2014
     * Description:Send mail to concered person
     * @param: $receiverId
     * @param: $ticketId
     */

    public static function sendDiscussionMail($receiverId, $ticketId) {
        $threads = TicketThread::where('ticket_id', $ticketId)->orderBy('id', 'desc')->get();
        $receiver = Sentry::findUserById($receiverId);
        Mail::send('ticket::emails.complaint-thread', array('threads' => $threads), function($message) use($receiver, $ticketId) {
            $message->to($receiver->email, $receiver->first_name . ' ' . $receiver->last_name)->subject('Comment on complaint [complaint-' . $ticketId . ']');
        });
    }

    /*
     * Created by: Amit Garg
     * Created on:05-May-2014
     * Description: To remove old message from mail
     *
     */

    public static function removeOldMessage($string) {
        try {
            $dom = new \DOMDocument();
            $dom->loadHTML($string);
            $dom->preserveWhiteSpace = false;

            $elements = $dom->getElementsByTagName('blockquote');
            while ($span = $elements->item(0)) {
                $span->parentNode->removeChild($span);
            }

            return $dom->saveHTML();
        } catch (ErrorException $ex) {
            return $string;
        }
    }

    /*
     * Created by: Amit Garg
     * Created on:05-May-2014
     * Description:
     *
     */

    public static function hyperlink($ticketId) {
        return '<a href="'.\URL::to('complaint-detail').'/'.$ticketId.'">'.$ticketId.'</a>';
    }
}
