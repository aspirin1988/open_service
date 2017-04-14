<?php

    namespace App\Http\Controllers;

    use App\Channel;
    use App\ChannelRelation;
    use App\City;
    use App\Event;
    use App\EventType;
    use App\Reminder;
    use Illuminate\Auth\Access\Response;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Cache;

    class EventsController extends Controller
    {
        public function calendar( $year = false, $month = false )
        {
            $month = (int)$month;

            $calendar = [];

            $_current_date = date( 'Y-m-d' );

            if( !$month ) {
                $month = date( 'm' );
            }
            else {
                if( $month < 10 ) {
                    $month = '0'.$month;
                }
            }
            if( !$year ) {
                $year = date( 'Y' );
            }

            $day_count = cal_days_in_month( CAL_GREGORIAN, $month, $year );
            $first_day = date( 'N', strtotime( $year.'-'.$month.'-1 00:00:00' ) );

            $week = 1;
            for( $i = 1; $i < $first_day; $i++ ) {
                $calendar[ $week ][] = [ 'day' => null ];
            }

            for( $i = 1; $i <= $day_count; $i++ ) {

                $str_i = $i;
                if( $str_i < 10 ) {
                    $str_i = "0".$i;
                }

                $date = $year.'-'.$month.'-'.$str_i;

                $day = date( 'l', strtotime( $date.' 00:00:00' ) );

                $events = Cache::remember( 'events_'.$date, 1, function() use ( $date ) {
                    return Event::where( 'the_date', '>=', $date.' 00:00:00' )
                        ->where( 'the_date', '<', $date.' 23:59:59' )
                        ->get();
                } );

                $calendar[ $week ][] = [ 'day'    => $day,
                                         'date'   => $date,
                                         'events' => $events,
                ];

                if( ( $first_day % 7 ) == 0 ) {
                    $week++;
                }
                $first_day++;
            }

            $week = count( $calendar );
            if( count( $calendar[ $week ] ) < 7 ) {
                for( $i = count( $calendar[ $week ] ); $i < 7; $i++ ) {
                    $calendar[ $week ][] = [ 'day' => null ];
                }
            }

            $cities = City::get();
            $types  = EventType::get();

            return view( 'event.calendar', [ 'that'         => $this,
                                             'query'        => $year.'-'.$month,
                                             'year'         => $year,
                                             'month'        => (int)$month,
                                             'calendar'     => $calendar,
                                             'current_date' => $_current_date,
                                             'cities'       => $cities,
                                             'types'        => $types,
                ]
            );
        }

        public function getCalendar( Request $request )
        {
            $month = $request->input( 'Month' );
            $year  = $request->input( 'Year' );

            $month = (int)$month;

            $calendar = [];

            $_current_date = date( 'Y-m-d' );

            if( !$month ) {
                $month = date( 'm' );
            }
            else {
                if( $month < 10 ) {
                    $month = '0'.$month;
                }
            }
            if( !$year ) {
                $year = date( 'Y' );
            }

            $day_count = cal_days_in_month( CAL_GREGORIAN, $month, $year );
            $first_day = date( 'N', strtotime( $year.'-'.$month.'-1 00:00:00' ) );

            $week = 1;
            for( $i = 1; $i < $first_day; $i++ ) {
                $calendar[ $week ][] = [ 'day' => null ];
            }

            for( $i = 1; $i <= $day_count; $i++ ) {

                $str_i = $i;
                if( $str_i < 10 ) {
                    $str_i = "0".$i;
                }

                $date = $year.'-'.$month.'-'.$str_i;

                $day = date( 'l', strtotime( $date.' 00:00:00' ) );

                $events = Cache::remember( 'events_'.$date, 1, function() use ( $date ) {
                    return Event::where( 'the_date', '>=', $date.' 00:00:00' )
                        ->where( 'the_date', '<', $date.' 23:59:59' )
                        ->get();
                } );

                $calendar[ $week ][] = [ 'day'    => $day,
                                         'date'   => $date,
                                         'events' => $events,
                ];

                if( ( $first_day % 7 ) == 0 ) {
                    $week++;
                }
                $first_day++;
            }

            $week = count( $calendar );
            if( count( $calendar[ $week ] ) < 7 ) {
                for( $i = count( $calendar[ $week ] ); $i < 7; $i++ ) {
                    $calendar[ $week ][] = [ 'day' => null ];
                }
            }

            return response()->json( $calendar );
        }

        public function EventList()
        {
            $cities = City::get();
            $types  = EventType::get();

            return view( 'event.event_list', [
                'that'   => $this,
                'cities' => $cities,
                'types'  => $types,
            ] );
        }

        public function EventListGet()
        {
            $data = Event::orderBy( 'the_date', 'DESC' )->get();

            return response()->json( $data );
        }

        public function add()
        {
            return view( 'event.add', [ 'that' => $this ] );
        }

        public function getEvent( Request $request )
        {
            $id   = $request->input( 'id' );
            $data = Event::where( 'id', $id )->first();
            $data->getReminders();
            $data->getChannels();

            return response()->json( ( $data ? $data : [] ) );

        }

        public function addEvent( Request $request )
        {

            $data = $request->input( 'data' );
            Cache::pull( 'events_'.$data[ 'the_date' ] );
            $reminders = $data [ 'reminders' ];
            $channels  = $data [ 'channels' ];
            unset( $data [ 'reminders' ] );
            unset( $data [ 'channels' ] );
            $data[ 'user_id' ] = Auth::user()->id;
            $data              = Event::create( $data );
            foreach( $reminders as $reminder ) {
                $data->addReminder( $reminder );
            }

            foreach( $channels as $channel ) {
                $data->addChannel( $channel[ 'channel' ] );
            }

            $data->Send();


            return response()->json( $data );
        }

        public function saveEvent( $id, Request $request )
        {
            $input     = $request->input( 'data' );
            $date      = $input[ 'the_date' ];
            $reminders = $input [ 'reminders' ];
            $channels  = $input [ 'channels' ];
            unset( $input [ 'reminders' ] );
            unset( $input [ 'channels' ] );

            $data = Event::where( 'id', $id )->first();

            Cache::pull( 'events_'.$date );
            Cache::pull( 'events_'.$data->the_date );
            $data->update( $input );
            foreach( $reminders as $reminder ) {
                if( isset( $reminder[ 'id' ] ) ) {
                    $data->updateReminder( $reminder );
                }
                else {
                    $data->addReminder( $reminder );
                }
            }

            foreach( $channels as $channel ) {
                if( isset( $channel[ 'id' ] ) ) {
                    $data->updateChannel( $channel[ 'id' ], $channel[ 'channel' ] );
                }
                else {
                    $data->addChannel( $channel[ 'channel' ] );
                }
            }

            return response()->json( $data );

        }

        public function deleteEvent( $id )
        {
            $data = Event::where( 'id', $id )->first();
            Cache::pull( 'events_'.$data->the_date );
            $data->deleteReminders();

            return response()->json( $data->delete() );
        }

        public function deleteReminder( $id )
        {
            $data = Reminder::where( 'id', $id )->first();

            return response()->json( $data->delete() );
        }

        public function calendarGet( Request $request )
        {
            $data = $request->all();
            $data = Event::where( 'the_date', '>=', $data[ 'date_start' ] )
                ->where( 'the_date', '<', $data[ 'date_end' ] )
                ->get();

            return response()->json( $data );
        }

        public function typeList()
        {
            $data = EventType::orderBy( 'name', 'DESC' )->get();

            return view( 'event.type_list', [ 'that' => $this, 'types' => $data ] );
        }

        public function typeAdd( Request $request )
        {
            $type = $request->input( 'name' );
            if( !empty( $type ) ) {
                EventType::create( [ 'name' => $type ] );
            }

            $data = EventType::orderBy( 'name', 'DESC' )->get();

            return view( 'event.type_list', [ 'that' => $this, 'types' => $data ] );
        }

        public function typeDelete( $id )
        {
            EventType::where( 'id', $id )->delete();

            return redirect()->to( '/admin/event/types' );
        }

        public function getChannelList( $id )
        {

            $data = Channel::whereNotIn( 'id', ChannelRelation::select( 'channel_id' )
                ->where( 'event_id', $id )->get() )
                ->select( 'channels.id', 'channels.name' )
                ->get();

            return response()->json( $data );
        }

        public function delChannel( $id )
        {
            return response()->json( ChannelRelation::where( 'id', $id )->delete() );
        }

        public function addChannel( $id, Request $request )
        {
            $chanel_id = $request->input( 'channel_id' );
            $data      = ChannelRelation::create( [ 'event_id' => $id, 'channel_id' => $chanel_id ] );

            return response()->json( $data );
        }

    }
