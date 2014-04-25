<?php

namespace Efusionsoft\Ticket\Controllers;

use Efusionsoft\Mis\Controllers\BaseController;
use View;
use Input;
use Sentry;
use Redirect;
use Config;
use Response;
use DB;
use Efusionsoft\Ticket\Models\Ticket;
use Efusionsoft\Ticket\Models\TicketThread;
use Efusionsoft\Ticket\Models\TicketAssignmentHistory;
use Efusionsoft\Ticket\Models\TicketCriticallityHistory;
use Efusionsoft\Ticket\Models\TicketStatusHistory;
use Efusionsoft\Ticket\Models\Setting;
use Sentry\Users\UserNotFoundException;

class SuggestController extends BaseController {
    /*
     * Created by: Amit Garg
     * Created on:18-Apr-2014
     * Description:
     *
     */

    public function getUsers($grpName) {
        $keyword=Input::get('q');
        $group = Sentry::findGroupByName($grpName);
        $users  = DB::table('users')
            ->join('users_groups', 'users.id', '=', 'users_groups.user_id')
            ->where( 'users_groups.group_id', '=', $group->id)
            ->where( 'users.first_name', 'like', "%$keyword%")
            ->orwhere( 'users.last_name', 'like', "%$keyword%")
            ->select('users.id', 'users.first_name', 'users.last_name')
            ->get();
    //print_r($users);
      //  die;
        
        //$users = Sentry::findAllUsersInGroup($group);
        $ret = array();
        $i = 0;
        foreach ($users as $user) {
            $ret[$i]['id'] = $user->id;
            $ret[$i++]['name'] = $user->first_name.' ' .$user->last_name .' ['.$user->id.']';
            
        }
        return Response::json($ret);
    }

}
