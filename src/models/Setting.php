<?php

namespace Efusionsoft\Ticket\Models;

class Setting extends \Eloquent {
    protected $table = 'settings';
    protected $guarded = array();
    
    /*
     * Created by: Amit Garg
     * Created on:03-May-2014
     * Description:
     *
     */

    public static function getPrioritiesArr() {
        $priorities  = Setting::where('entity','=','complaint')->where('key','=','priority')->get();
        $prArr = array();
        foreach ($priorities as $prior){
            $val = json_decode($prior->value);
            $prArr[$val->title]=$val->color;
        }
        return $prArr;
    }
}