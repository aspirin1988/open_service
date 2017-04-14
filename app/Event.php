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

        public function addChannel( $channel )
        {
            $data = [
                'channel_id' => $channel[ 'id' ],
                'event_id'   => $this->id,
            ];

            $data = ChannelRelation::create( $data );

            return $data;
        }

        public function updateChannel( $id, $channel )
        {
            $data = [
                'channel_id' => $channel[ 'id' ],
                'event_id'   => $this->id,
            ];

            $data = ChannelRelation::where( 'id', $id )->update( $data );

            return $data;
        }

        public function getChannels()
        {
            $data = ChannelRelation::where( 'event_id', $this->id )->get();

            foreach( $data as $key => $value ) {
                $data[ $key ]->getChannel();
            }

            $this->channels = $data;

            return $data;
        }

        public function deleteReminders()
        {
            $data = Reminder::where( 'event_id', $this->id )->delete();

            return $data;
        }

        public function Send()
        {
            $this->getChannels();

            $text = '';
            $text .= ( !empty( $this->name ) ? "<strong>".$this->name."</strong> " : '' ).( !empty( $this->getTypeName() ) ? ' <strong>'.$this->getTypeName()."</strong>\n" : '' );
            $text .= ( !empty( $this->the_date ) ? '📆 Будет проводиться : '.$this->the_date."" : '' ).( !empty( $this->time ) ? ',начало: в '.$this->time."\n" : '' );
            $text .="Место проведения : ". ( !empty( $this->getCityName() ) ? ' <b>'.$this->getCityName()."</b>" : '' ).( !empty( $this->address ) ? ' ,<b>'.$this->address."</b>\n" : '' );
            $text .= ( !empty( $this->registration_date ) ? 'Регистрация до : '.$this->registration_date.".\n" : '' );
            $text .="Подробности ". ( !empty( $this->link ) ? ' '.$this->link."\n" : '' );
            $text .= ( !empty( $this->content ) ? $this->content."\n" : '' );

            foreach( $this->channels as $key => $channel ) {
                Bot::send( $channel->channel->link, $text );
            }
        }

    }
