<?php

namespace Efusionsoft\Ticket\Controllers;

use Efusionsoft\Mis\Controllers\BaseController;
use View;
use Input;
use Sentry;
use Redirect;
use Config;
use Response;
use Efusionsoft\Ticket\Models\Ticket;
use Efusionsoft\Ticket\Models\TicketThread;
use Efusionsoft\Ticket\Models\TicketAssignmentHistory;
use Efusionsoft\Ticket\Models\TicketCriticallityHistory;
use Efusionsoft\Ticket\Models\TicketStatusHistory;
use Efusionsoft\Ticket\Models\Setting;
use Mail;
use Sentry\Users\UserNotFoundException;

//use Ticket;
class SettingController extends BaseController {
    
    /*
     * Created by: Amit Garg
     * Created on:17-Apr-2014
     * Description:
     *
     */

    public function complaint() {
        
        $priorities  = Setting::where('entity','=','complaint')->where('key','=','priority')->get();
        
        $staff = $this->getUsersByGroupName(Config::get('ticket::staff_group'));
        $clientManagers = $this->getUsersByGroupName(Config::get('ticket::client_rel_manager'))->toArray();
        $maidManagers = $this->getUsersByGroupName(Config::get('ticket::maid_rel_manager'))->toArray();
        $passed = array('priorities'=>$priorities, 'staff'=>$staff , 'clientMgrId'=>$clientManagers[0]['id'], 'maidMgrId'=>$maidManagers[0]['id']);
        $passed['clientColor'] = Setting::where('entity','=','complaint')->where('key','=','client_manager_color_code')->get()->toArray()[0]['value'];
        $passed['maidColor'] = Setting::where('entity','=','complaint')->where('key','=','maid_manager_color_code')->get()->toArray()[0]['value'];
        $this->layout = View::make(Config::get('ticket::views.settings'),$passed);
        $this->layout->title = 'Complaint Settings';
        $this->layout->breadcrumb = Config::get('ticket::breadcrumbs.settings');
    }
    
    /*
     * Created by: Amit Garg
     * Created on:17-Apr-2014
     * Description:
     *
     */

    public function addPriority() {
        $data = Input::all();
        $returnData = ['status'=>FALSE,'message'=>'Form data has not been filled'];
        if($data['title'] && $data['color'] ){
            $priorityArr =['entity'=>'complaint', 'key'=>'priority', 'value'=>  json_encode($data)];
            $res  = Setting::create($priorityArr);
            if($res->id){
                $returnData['status'] = TRUE;
                $returnData['message']='Priority has been added successfully';
            }else {
                 $returnData['status'] = FALSE;
                $returnData['message']='Priority can not be added';
            }
        }
         return Response::json($returnData);
    }
     /*
     * Created by: Amit Garg
     * Created on:17-Apr-2014
     * Description:
     *
     */
    public function deletePriority(){
        $id = Input::get('id');
        $setting = Setting::find($id);
        $setting->delete();
        $ret = array('status' => true, 'message' => 'Priority has been closed.');
        return Response::json($ret);
    }
     /*
     * Created by: Amit Garg
     * Created on:17-Apr-2014
     * Description:
     *
     */
    public function getUsersByGroupName($grpName){
        $group = Sentry::findGroupByName($grpName);
        $users = Sentry::findAllUsersInGroup($group);
        return $users;
    }
    
    /*
     * Created by: Amit Garg
     * Created on:17-Apr-2014
     * Description:
     *
     */

    public function updateAssignees() {
        $inData = Input::all();
        $this->addUniqueUserToGroup(Config::get('ticket::client_rel_manager'), $inData['client_manager']);
        $this->addUniqueUserToGroup(Config::get('ticket::maid_rel_manager'), $inData['maid_manager']);
        $clientManagers = Setting::where('entity','=','complaint')->where('key','=','client_manager_color_code')->get();
        $maidManagers = Setting::where('entity','=','complaint')->where('key','=','maid_manager_color_code')->get();
        foreach ($clientManagers as $client) {
            $client->value=$inData['color_client_manager'];
            $client->save();
        }
        
        foreach ($maidManagers as $maid) {
            $maid->value=$inData['color_maid_manager'];
            $maid->save();
        }
        $ret = array('status' => true, 'message' => 'Assignee have been saved.');
        return Response::json($ret);
    }
    
    /*
     * Created by: Amit Garg
     * Created on:17-Apr-2014
     * Description:
     *
     */

    public function addUniqueUserToGroup($grpName, $userId) {
        if($grpName && $userId){
            $group = Sentry::findGroupByName($grpName);
            $users = Sentry::findAllUsersInGroup($group);
            foreach ($users as $user) {
                $user->removeGroup($group);
            }
            $newUser = Sentry::findUserById($userId);
            $newUser->addGroup($group);
        }
        
    }
}