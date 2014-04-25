<?php
/**
 * Loggued routes without permission
 */

/**
 * Loggued routes with permissions
 */
Route::group(array('before' => 'basicAuth|hasPermissions'), function()
{
    /**
     * Ticket Listing
     */
    Route::get('complaints', array(
        'as' => 'listTickets',
        'uses' => 'Efusionsoft\Ticket\Controllers\TicketController@getIndex')
    );
    
    
     /**
     * To Delete ticket
     */
    Route::delete('complaints', array(
        'as' => 'addTicket',
        'uses' => 'Efusionsoft\Ticket\Controllers\TicketController@close')
    );
    
    
     /*
     * Add New Ticket Form
     */
    
    Route::get('complaint/new',array(
        'as' => 'addTicket',
        'uses' => 'Efusionsoft\Ticket\Controllers\TicketController@addForm')
            );
    
     Route::put('complaint/new',array(
        'as' => 'addTicket',
        'uses' => 'Efusionsoft\Ticket\Controllers\TicketController@insertNewTicket')
            );
    
     /**
     * Tickte Details
     */
    Route::get('complaint-detail/{ticket_id}', array(
        'as' => 'ticketDetail',
        'uses' => 'Efusionsoft\Ticket\Controllers\TicketController@getView')
    );
    /*
     * Add comment and change status
     */
   Route::post('complaint-detail/{ticket_id}', array(
        'as' => 'ticketDetail',
        'uses' => 'Efusionsoft\Ticket\Controllers\TicketController@postComment')
    );
   
   Route::post('ticket-close/{ticket_id}', array(
        'as' => 'ticketDetail',
        'uses' => 'Efusionsoft\Ticket\Controllers\TicketController@close')
    );
   
   Route::get('settings/complaints',array(
        'as' => 'ticketSettings',
        'uses' => 'Efusionsoft\Ticket\Controllers\SettingController@complaint')
    );
   
   Route::put('settings/complaints',array(
        'as' => 'addticketSettings',
        'uses' => 'Efusionsoft\Ticket\Controllers\SettingController@addPriority')
    );
   Route::delete('settings/complaints',array(
        'as' => 'addticketSettings',
        'uses' => 'Efusionsoft\Ticket\Controllers\SettingController@deletePriority')
    );
   
   Route::post('settings/complaints',array(
        'as' => 'addticketSettings',
        'uses' => 'Efusionsoft\Ticket\Controllers\SettingController@updateAssignees')
    );
   
   Route::get('suggest/users/{type}',array(
      'as' => 'ticketDetail',
       'uses'=> 'Efusionsoft\Ticket\Controllers\SuggestController@getUsers'
   ));
    
});

 Route::get('cron', array(
        'as' => 'cron',
        'uses' => 'Efusionsoft\Ticket\Controllers\TicketController@cron')
    );