<?php

    namespace App;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\Auth;

    class Event extends Model
    {
        public $timestamps = true;

        protected $guarded = [];

        public function getReminders()
        {
            $data = Reminder::where( 'event_id', $this->id )->get();

            $this->reminders = $data;

            return $data;
        }

        public function getTypeName()
        {
            $data = EventType::where( 'id', $this->event_type )->first();

            $this->event_type_name = $data->name;

            return $data->name;
        }

        public function getCityName()
        {
            $data = City::where( 'id', $this->city )->first();

            $this->city_name = $data->name;

            return $data->name;
        }

        public function addReminder( $reminder )
        {
            $reminder[ 'event_id' ] = $this->id;
            $reminder[ 'user_id' ]  = Auth::user()->id;
            if( isset( $reminder[ 'send_time' ] ) ) {
                unset( $reminder[ 'send_time' ] );
            }
            $data = Reminder::create( $reminder );

            return $data;
        }

        public function updateReminder( $reminder )
        {
            if( isset( $reminder[ 'send_time' ] ) ) {
                unset( $reminder[ 'send_time' ] );
            }
            $data = Reminder::where( 'id', $reminder[ 'id' ] )->update( $reminder );

            return $data;
        }

        public function deleteReminders()
        {
            $data = Reminder::where( 'event_id', $this->id )->delete();

            return $data;
        }
    }
