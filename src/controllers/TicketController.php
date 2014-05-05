<?php

namespace Efusionsoft\Ticket\Controllers;

use Efusionsoft\Mis\Controllers\BaseController;
use View;
use Input;
use Sentry;
use Redirect;
use Config;
use Response;
use Request;
use Efusionsoft\Ticket\Models\Ticket;
use Efusionsoft\Ticket\Models\TicketThread;
use Efusionsoft\Ticket\Models\TicketAssignmentHistory;
use Efusionsoft\Ticket\Models\TicketCriticallityHistory;
use Efusionsoft\Ticket\Models\TicketStatusHistory;
use Mail;
use Sentry\Users\UserNotFoundException;
use Efusionsoft\Ticket\Models\Setting;
use Efusionsoft\Ticket\Models\TicketEventLog;

//use Ticket;
class TicketController extends BaseController {

    /**
     * Index loggued page
     */
    public function getIndex() {
        $ticket = new Ticket();
        $list = $ticket->getTicketList(); //It  fetch upto 20 resulst
        //$list = Ticket::paginate(20);
        //$res = $list->toArray();
        //print_r($res);die;
        $this->layout = View::make(Config::get('ticket::views.ticket-index'), array('tickets' => $list));
        $this->layout->title = trans('ticket::ticket.titles.list');
        $this->layout->breadcrumb = Config::get('ticket::breadcrumbs.tickets');
    }

    /**
     * Index loggued page
     */
    public function getView($id) {

        try {

            $ticket = new Ticket();
            if (!$ticket->exists($id)) {
                throw new Exception('No such ticket exist.');
            }
            $data = $ticket->getTicket($id);
            $ticketObj = Ticket::find($data[0]['id']);
            $generator = $ticketObj->generator;
            $raisedBy = Ticket::find($data[0]['id'])->raisedBy;
            $raisedFor = $ticketObj->raisedFor;
            $threads = TicketThread::where('ticket_id', $ticketObj->id)->orderBy('id', 'desc')->get();
            //$threads = array_reverse($threads);
            $group = Sentry::findGroupByName(Config::get('ticket::maids_group'));
            $workers = Sentry::findAllUsersInGroup($group)->toArray();
            $criticalArr = Config::get('ticket::critical_status'); //Priorities
        } catch (Exception $e) { // need to check Exception class
        }
        if (Request::ajax()) {
            $this->layout = View::make('ticket::ticket.discussion', array('ticket' => $data[0], 'gen' => $generator,
                        'raisedFor' => $raisedFor,
                        'raisedBy' => $raisedBy,
                        'threads' => $threads,
                        'workers' => $workers,
                        'critical' => $criticalArr
            ));
        } else {
            $this->layout = View::make(Config::get('ticket::views.ticket-view'), array('ticket' => $data[0], 'gen' => $generator,
                        'raisedFor' => $raisedFor,
                        'raisedBy' => $raisedBy,
                        'threads' => $threads,
                        'workers' => $workers,
                        'critical' => $criticalArr
            ));
        }

        $this->layout->title = trans('ticket::ticket.titles.list');
        $this->layout->breadcrumb = Config::get('ticket::breadcrumbs.tickets');
    }

    /*
     * Created by: Amit Garg
     * Created on:8-Apr-2014
     * Description:
     *
     */

    public function addForm() {
        $group = Sentry::findGroupByName(Config::get('ticket::staff_group')); //List of Maid Relationship Manager
        $workers = Sentry::findAllUsersInGroup($group)->toArray();
        //$criticalArr = Config::get('ticket::critical_status');
        //print_r($criticalArr);die;
        $clientManager = $this->getUsersByGroupName(Config::get('ticket::client_rel_manager'))->toArray()[0];
        $maidManager = $this->getUsersByGroupName(Config::get('ticket::maid_rel_manager'))->toArray()[0];
        $priorities = Setting::where('entity', '=', 'complaint')->where('key', '=', 'priority')->get();
        $clientComplaintType = Setting::where('entity', '=', 'complaint')->where('key', '=', 'complaints_type_by_client')->first()->value;
        $maidComplaintType = Setting::where('entity', '=', 'complaint')->where('key', '=', 'complaints_type_by_maid')->first()->value;

        $this->layout = View::make(Config::get('ticket::views.ticket-new'), array('workers' => $workers, 'critical' => $priorities,
                    'clientCompType' => $clientComplaintType,
                    'maidCompType' => $maidComplaintType
        ));

        $this->layout->title = trans('ticket::ticket.titles.list');
        $this->layout->breadcrumb = Config::get('ticket::breadcrumbs.tickets');
        $this->layout->clientManager = $this->getUsersByGroupName(Config::get('ticket::client_rel_manager'))->toArray()[0];
        $this->layout->maidManager = $this->getUsersByGroupName(Config::get('ticket::maid_rel_manager'))->toArray()[0];
    }

    /*
     * Created by: Amit Garg
     * Created on:04-April-2014
     * Description:Create new ticket
     *
     */

    public function insertNewTicket() {
        $res = Input::all();
        $ret = array('status' => false);
        $user = Sentry::getUser();
        if ($userId = $user->getId()) {

            if (!$res['title']) {
                $ret['message'] = 'Title is required';
                return Response::json($ret);
            }

            if (!$res['comment']) {
                $ret['message'] = 'Comment is required';
                return Response::json($ret);
            }

            if (!$res['time']) {
                $ret['message'] = 'Time is required';
                return Response::json($ret);
            }
            if (empty($res['complained_against_name'])) {
                $ret['message'] = 'Please choose name';
                return Response::json($ret);
            }
            if (!$res['assigned_to_id']) {
                $ret['message'] = 'Ticket must be assigned';
                return Response::json($ret);
            }
            $against = $res['complained_against_name'];
            $regexId = '/^.* \[(?P<id>\d*)\]$/'; // regex to allow only alphanumeric, comma, period and space
            preg_match($regexId, $against, $matches); // verify regex
            $raiseForId = 0;
            if (empty($matches['id'])) {
                $ret['message'] = 'Ticket must be assigned to id';
                return Response::json($ret);
            } else {
                $raiseForId = $matches['id'];
            }

            $byName = $res['complained_by_name'];
            preg_match($regexId, $byName, $matches); // verify regex
            $raiseById = 0;
            if (!empty($matches['id'])) {
                $raiseById = $matches['id'];
            }
            //Complaint type has been added on 5th May-2014
            $complaintType = $res['complaint_type'];

            $arr = array('generated_by_id' => $userId,
                'title' => $res['title'],
                'raised_by_id' => $raiseById,
                'raised_for_id' => $raiseForId,
                'assigned_to_id' => $res['assigned_to_id'],
                'complaint_type' => $complaintType,
                'ticket_date' => $res['time']
            );

            $insArr = Ticket::create($arr);

            if ($ticketId = $insArr->id) {
                $ret['status'] = TRUE;
                $ret['message'] = 'Ticket has been created successfully';
                $threadArr = array('ticket_id' => $ticketId,
                    'title' => $res['title'],
                    'comment' => $res['comment'],
                    'commented_by_id' => $userId
                );
                TicketThread::create($threadArr);
                $histArr = Array('ticket_id' => $ticketId,
                    'assigned_to_id' => $res['assigned_to_id']
                );
                TicketAssignmentHistory::create($histArr);
                $statusArr = Array('ticket_id' => $ticketId,
                    'status' => 'New'
                );
                TicketStatusHistory::create($statusArr);
                $criticalArr = Array('ticket_id' => $ticketId,
                    'criticallity' => $res['criticallity']
                );
                TicketCriticallityHistory::create($criticalArr);

                $assignee = Sentry::findUserById($res['assigned_to_id']);
                $toName = $assignee->first_name . ' ' . $assignee->last_name;
                //Event log for ticket creation
                $eventArr = ['ticket_id' => $ticketId, 'created_by_id' => $user->id, 'message' => 'Complaint ' . Ticket::hyperlink($ticketId) . ' has been submitted through interface by ' . $user->first_name . ' ' . $user->last_name];
                TicketEventLog::create($eventArr);
                Mail::send(Config::get('ticket::views.email-new-complaint'), array('name' => $toName, 'title' => $res['title'], 'comment' => $res['comment']), function($message) use($assignee, $ticketId, $toName) {
                    $message->to($assignee->email, $toName)->subject('New Complaint has been created [complaint-' . $ticketId . ']');
                });
            }
            return Response::json($ret);
        } else {
            $ret['message'] = 'Unauthorzed user';
            return Response::json($ret);
        }
    }

    /*
     * Created by: Amit Garg
     * Created on:9-Apr-2014
     * Description: Comment in a ticket
     *
     */

    public function postComment($ticketId) {
        $ret = array('status' => FALSE);
        if ((int) $ticketId) {
            $ticket = Ticket::find($ticketId);
            $statusArr = $ticket->status->sortBy('created_at')->toArray();

            $currentSt = last($statusArr);

            $data = Input::all();
            $user = Sentry::getUser();
            $userId = $user->getId();
            if ($data['comment']) {
                TicketThread::create(array('ticket_id' => $ticketId,
                    'comment' => $data['comment'], 'commented_by_id' => $userId));
                $eventArr = ['ticket_id' => $ticketId, 'created_by_id' => $user->id, 'message' => $user->first_name . ' ' . $user->last_name . ' has commented on  complaint ' . Ticket::hyperlink($ticketId) . ' through interface'];
                TicketEventLog::create($eventArr);
                if ($currentSt['status'] != $data['ticket_status']) {
                    TicketStatusHistory::create(array('ticket_id' => $ticketId, 'status' => $data['ticket_status']));
                    $eventArr = ['ticket_id' => $ticketId, 'created_by_id' => $user->id, 'message' => $user->first_name . ' ' . $user->last_name . ' has changed status of complaint ' . Ticket::hyperlink($ticketId) . ' from '.$currentSt['status']. ' to '.$data['ticket_status']];
                    TicketEventLog::create($eventArr);
                }
                $this->reAssignTicket($ticketId, $data['assigned_to_id']);

                $this->changePriority($ticketId, $data['criticallity']);

                $ret['status'] = TRUE;
                $ret['message'] = 'Status has updated successfully';
                //Sending mail to the person(Client or Maid) who raised the  
                Ticket::sendDiscussionMail($ticket->raised_by_id, $ticket->id);
            } else {
                $ret['message'] = 'Comment is required';
            }
        } else {
            $ret['message'] = 'Invalid ticket id';
        }

        return Response::json($ret);
    }

    /*
     * Created by: Amit Garg
     * Created on:9-Apr-2014
     * Description:
     *
     */

    public function reAssignTicket($ticketId, $assigneeId) {
        $ticket = Ticket::find($ticketId);
        $user = Sentry::getUser();
        if ($ticket->assigned_to_id != $assigneeId) {
            $newUser = Sentry::findUserById($assigneeId);
            $ticket->assigned_to_id = $assigneeId;
            $ticket->save();
            $histArr = Array('ticket_id' => $ticketId,
                'assigned_to_id' => $assigneeId
            );
            TicketAssignmentHistory::create($histArr);
            $eventArr = ['ticket_id' => $ticketId, 'created_by_id' => $user->id, 'message' => $user->first_name . ' ' . $user->last_name . ' has assigned complaint ' . Ticket::hyperlink($ticketId) . ' to '.$newUser->first_name. ' '.$newUser->last_name];
            TicketEventLog::create($eventArr);
            
            $ticket->assigned_to_id = $assigneeId;
            $ticket->save();
        }
    }

    /*
     * Created by: Amit Garg
     * Created on:9-Apr-2014
     * Description:
     *
     */

    /*
     * Created by: Amit Garg
     * Created on:11-Apr-2014
     * Description:
     *
     */

    public function changePriority($ticketId, $priority) {
        $ticket = Ticket::find($ticketId);
        $user = Sentry::getUser();
        $priorities = $ticket->criticallity->sortBy('created_at')->toArray();
        $currentPriority = last($priorities);
        if ($currentPriority['criticallity'] != $priority) {
            $prioArr = array('criticallity' => $priority, 'ticket_id' => $ticketId);
            TicketCriticallityHistory::create($prioArr);
            $eventArr = ['ticket_id' => $ticketId, 'created_by_id' => $user->id, 'message' => $user->first_name . ' ' . $user->last_name . ' has changed priority of complaint ' . Ticket::hyperlink($ticketId) . ' from '.$currentPriority['criticallity']. ' to '.$priority];
            TicketEventLog::create($eventArr);
        }
    }

    /*
     * Created by: Amit Garg
     * Created on:11-Apr-2014
     * Description:To close the complaint
     *
     */

    public function close() {
        $ticketId = Input::get('id');
        $ticket = Ticket::find($ticketId);
        $user = Sentry::getUser();
        $eventArr = ['ticket_id' => $ticketId, 'created_by_id' => $user->id, 'message' => $user->first_name . ' ' . $user->last_name . ' has closed complaint ' . Ticket::hyperlink($ticketId) . ' from '.$currentPriority['criticallity']];
        TicketEventLog::create($eventArr);
        $ticket->delete();
        $ret = array('status' => true, 'message' => 'Ticket has been closed.');
        return Response::json($ret);
    }

    /*
     * Created by: Amit Garg
     * Created on:11-Apr-2014
     * Description: receive latest emails
     *
     */

    public function getLatestMails() {
        $host = Config::get('ticket::receiver_host');
        $uname = Config::get('ticket::receiver_uname');
        $pass = Config::get('ticket::receiver_pass');
        $inbox = imap_open($host, $uname, $pass) or die('Cannot connect to Gmail: ' . imap_last_error());


        /* grab emails */
        $emails = imap_search($inbox, 'NEW');
        //print_r($emails);//die;

        $output = '';
        $mailArr = array();
        if (!empty($emails)) {
            $i = 0;
            foreach ($emails as $email_number) {
                $overview = imap_fetch_overview($inbox, $email_number, 0);
                //$message = imap_fetchbody($inbox, $email_number, '2',FT_PEEK);
                $structure = imap_fetchstructure($inbox, $email_number);
                //print_r($structure);
                $mailArr[$i]['subject'] = $subject = $overview[0]->subject;
                $mailArr[$i]['from'] = substr($overview[0]->from, strpos($overview[0]->from, '<') + 1, strpos($overview[0]->from, '>') - strpos($overview[0]->from, '<') - 1); //Parsing email 

                if (isset($structure->parts) && is_array($structure->parts) && isset($structure->parts[1])) {
                    $part = $structure->parts[1];
                    $message = imap_fetchbody($inbox, $email_number, 2);

                    if ($part->encoding == 3) {
                        $message = imap_base64($message);
                    } else if ($part->encoding == 1) {
                        $message = imap_8bit($message);
                    } else {
                        $message = imap_qprint($message);
                    }
                    $mailArr[$i]['message'] = $message;
                }
                $i++;
            }
        }
        return $mailArr;
    }

    /*
     * Created by: Amit Garg
     * Created on:11-April-2014
     * Description:
     *
     */

    public function cron() {
        //echo 'started';die;
        error_reporting(0);
        $mails = $this->getLatestMails();
        print_r($mails);
        if (count($mails)) {
            foreach ($mails as $mail) {
                $pattern = '/^.*\[complaint\-(?P<id>\d+)\].*$/';
                if (preg_match($pattern, $mail['subject'], $matches)) {
                    $ticketId = $matches['id'];
                    $ticket = Ticket::find($ticketId);
                    try {
                        $user = Sentry::findUserByLogin($mail['from']);
                        TicketThread::create(array('ticket_id' => $ticketId,
                            'comment' => Ticket::removeOldMessage($mail['message']), 'commented_by_id' => $user->id));
                        
                        //Ticket
                        Ticket::sendDiscussionMail($ticket['assigned_to_id'], $ticketId);
                         $eventArr = ['ticket_id' => $ticketId, 'created_by_id' => $user->id, 'message' => $user->first_name . ' ' . $user->last_name . ' has commented on  complaint ' . Ticket::hyperlink($ticketId) . ' through mail'];
                         TicketEventLog::create($eventArr);
                    } catch (UserNotFoundException $ex) {
                        echo $mailArr['from'] . ' not foud';
                    }
                } else {
                    $res = $this->createNewComplaint($mail);
                }
            }
        }
        die;
    }

    /*
     * Created by: Amit Garg
     * Created on:11-Apr-2014
     * Description:
     *
     */

    public function createNewComplaint($mailArr) {
        //$sender = Sentry::findUserByLogin($mailArr['from']);

        $ret['status'] = false;
        $ret['message'] = 'Ticket can not be created';
        try {
            $user = Sentry::findUserByLogin($mailArr['from']);
            $group = Sentry::findGroupByName(Config::get('ticket::client_rel_manager'));
            $crms = Sentry::findAllUsersInGroup($group)->toArray();
            $arr = array('generated_by_id' => $user->id,
                'title' => $mailArr['subject'],
                'raised_by_id' => $user->id,
                'raised_for_id' => $crms[0]['id'],
                'ticket_date' => date('Y-m-d H:i:s')
            );

            $insArr = Ticket::create($arr);
            if ($ticketId = $insArr->id) {
                $ret['status'] = TRUE;
                $ret['message'] = 'Ticket has been created successfully';
                $threadArr = array('ticket_id' => $ticketId,
                    'title' => $mailArr['subject'],
                    'comment' => $mailArr['message'],
                    'commented_by_id' => $user->id
                );
                TicketThread::create($threadArr);
                $histArr = Array('ticket_id' => $ticketId,
                    'assigned_to_id' => $crms[0]['id']
                );
                TicketAssignmentHistory::create($histArr);
                $statusArr = Array('ticket_id' => $ticketId,
                    'status' => 'New'
                );
                TicketStatusHistory::create($statusArr);
                $criticalArr = Array('ticket_id' => $ticketId,
                    'criticallity' => 'Normal'
                );
                TicketCriticallityHistory::create($criticalArr);

                $toName = $crms[0]['first_name'] . ' ' . $crms[0]['last_name'];
                $eventArr = ['ticket_id' => $ticketId, 'created_by_id' => $user->id, 'message' => 'Complaint ' . $ticketId . ' has been submitted through email by ' . $user->first_name . ' ' . $user->last_name];
                TicketEventLog::create($eventArr);
                Mail::send(Config::get('ticket::views.email-new-complaint'), array('name' => $toName, 'title' => $mailArr['subject'], 'comment' => $mailArr['message']), function($message) use( $ticketId, $toName, $crms) {
                    $message->to($crms[0]['email'], $toName)->subject('New Complaint has been created [complaint-' . $ticketId . ']');
                });
                return $ret;
            }
        } catch (UserNotFoundException $e) {
            print_r($e);
            $e->message;
            die;
            echo $arr['from'] . '  was not found.';
        }
    }

    public function test() {
        echo Ticket::hyperlink(123);

        die;
    }

    /*
     * Created by: Amit Garg
     * Created on:17-Apr-2014
     * Description:
     *
     */

    public function settings() {
        echo 'yes';
        die;
    }

    /*
     * Created by: Amit Garg
     * Created on:17-Apr-2014
     * Description:
     *
     */

    public function getUsersByGroupName($grpName) {
        $group = Sentry::findGroupByName($grpName);
        $users = Sentry::findAllUsersInGroup($group);
        return $users;
    }

    /*
     * Created by: Amit Garg
     * Created on:02-May-2014
     * Description:
     *
     */

    public function latestTickets() {
        $ticket = new Ticket();
        $cnt = Config::get('ticket::config.latest-complaints-show-count');
        $list = $ticket->getLatestTicketList($cnt);
        $prioritiesArr = Setting::getPrioritiesArr();
        //print_r($prioritiesArr);die;
        $this->layout = View::make('ticket::common.complaints', array('tickets' => $list, 'prioArr' => $prioritiesArr));
    }

    /*
     * Created by: Amit Garg
     * Created on:02-May-2014
     * Description:
     *
     */

    public function getTicketCountByAssigneeId($assgneeId) {
        
    }

}
