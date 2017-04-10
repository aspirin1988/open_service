<?php

    Route::get( '/', function() {
        return view( 'welcome',['that'=>$this] );
    } );

    Auth::routes();

    Route::group( [ 'middleware' => [ 'auth' ] ], function() {

        Route::get( '/home', 'HomeController@index' );

        Route::get( '/admin/cities/', 'CityController@index' );
        Route::post( '/admin/cities/', 'CityController@add' );
        Route::get( '/admin/city/delete/{id}', 'CityController@delete' );


        Route::get( '/admin/event/types/', 'EventsController@typeList' );
        Route::post( '/admin/event/types/', 'EventsController@typeAdd' );
        Route::get( '/admin/event/types/delete/{id}', 'CityController@typeDelete' );

        Route::get( '/admin/events/', 'EventsController@EventList' );
        Route::post( '/admin/events/get/list', 'EventsController@EventListGet' );



        Route::get( '/admin/events/calendar', 'EventsController@calendar' );
        Route::get( '/admin/events/calendar/{year}/{month}/', 'EventsController@calendar' );
        Route::post( '/admin/events/get/calendar', 'EventsController@getCalendar' );

        Route::post( '/admin/event/get', 'EventsController@getEvent' );
        Route::post( '/admin/event/add/', 'EventsController@addEvent' );
        Route::post( '/admin/event/save/{id}', 'EventsController@saveEvent' );
        Route::post( '/admin/events/get', 'EventsController@calendarGet' );
        Route::delete( '/admin/event/delete/{id}', 'EventsController@deleteEvent' );
        Route::delete( '/admin/reminder/delete/{id}', 'EventsController@deleteReminder' );

//        Route::get( '/admin/event/add', 'EventsController@add' );

        Route::get( '/admin/users', 'EventsController@index' );
        Route::get( '/admin/user/add', 'EventsController@add' );

    } );
