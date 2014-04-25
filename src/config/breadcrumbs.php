<?php 
$ticketBreadArr = array(
            'title' => trans('ticket::breadcrumbs.tickets'),
            'link' => URL::route('listTickets'),
            'icon' => 'glyphicon-user',
        );
return array(
    'tickets' => array(
        $ticketBreadArr
    ),
    
    'settings' => array(
        
        array(
            'title' => 'Settings',
            'link' => URL::route('ticketSettings'),
            'icon' => 'glyphicon-user',
        ),
        $ticketBreadArr,
    ),
);