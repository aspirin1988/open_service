<?php

    namespace App\Http\Controllers;

    use App\City;
    use App\Event;
    use App\EventType;
    use App\Reminder;
    use Illuminate\Auth\Access\Response;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Cache;
    use App\Bot;

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

            return response()->json( ( $data ? $data : [] ) );

        }

        public function addEvent( Request $request )
        {

            $data = $request->input( 'data' );
            Cache::pull( 'events_'.$data[ 'the_date' ] );
            $reminders = $data [ 'reminders' ];
            unset( $data [ 'reminders' ] );
            $data[ 'user_id' ] = Auth::user()->id;
            $data              = Event::create( $data );
            foreach( $reminders as $reminder ) {
                $data->addReminder( $reminder );
            }
            $text = '';
            $text .= "<strong>".$data->name."</strong>\n";
            $text .= '🔔  '.$data->getTypeName()."\n";
            $text .= '📆 Дата проведения<pre>   '.$data->the_date."</pre>\n";
            $text .= '📆 Начало регистрации<pre>   '.$data->registration_date."</pre>\n";
            $text .= '🏙 Город : <b>'.$data->getCityName()."</b>\n\n";
            $text .= '📍 Место : <b>'.$data->address."</b>\n\n";
            $text .= '🕐 Время<pre>   '.$data->time."</pre>\n";
            $text .= '🔗 Ссылка '.$data->link."\n\n";
            $text .= $data->content."\n";

            Bot::send( '@op_it_test', $text );

            return response()->json( $data );
        }

        public function saveEvent( $id, Request $request )
        {
            $input     = $request->input( 'data' );
            $date      = $input[ 'the_date' ];
            $reminders = $input [ 'reminders' ];
            unset( $input [ 'reminders' ] );

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
    }
