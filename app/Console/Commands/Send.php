<?php

    namespace App\Console\Commands;

    use App\Bot;
    use App\Event;
    use App\Reminder;
    use Illuminate\Console\Command;
    use Symfony\Component\Console\Input\InputOption;
    use Symfony\Component\Console\Input\InputArgument;

    class Send extends Command
    {
        /**
         * The name and signature of the console command.
         *
         * @var string
         */
        protected $signature = 'send:run';

        /**
         * The console command description.
         *
         * @var string
         */
        protected $description = 'Command description';

        /**
         * Create a new command instance.
         *
         * @return void
         */
        public function __construct()
        {
            parent::__construct();
        }

        /**
         * Execute the console command.
         *
         * @return mixed
         */
        public function handle()
        {
            date_default_timezone_set('Asia/Almaty');
            $now        = time();
            $date_start = date( 'Y-m-d H:i', $now );
            $date_end   = date( 'Y-m-d H:i', ( $now + 60 * 60 ) );
            var_dump($date_start);
            var_dump($date_end);
            var_dump(date( 'Y-m-d H:i'));

            $reminders = Reminder::where( 'send_date', '>=', $date_start )->where( 'send_date', '<', $date_end )->where( 'done', 0 )->where( 'active', 1 )->get();

            foreach( $reminders as $reminder ) {
                $data = Event::where( 'id', $reminder->event_id )->first();
                $text = '';
                $text .= ( !empty( $data->name ) ?              "<strong>".$data->name."</strong>\n" : '' );
                $text .= ( !empty( $data->getTypeName() ) ?     '🔔  '.$data->getTypeName()."\n" : '' );
                $text .= ( !empty( $data->the_date ) ?          '📆 Дата проведения : '.$data->the_date."\n" : '' );
                $text .= ( !empty( $data->registration_date ) ? '📆 Начало регистрации : '.$data->registration_date."\n" : '' );
                $text .= ( !empty( $data->getCityName() ) ?     '🏙 Город : <b>'.$data->getCityName()."</b>\n" : '' );
                $text .= ( !empty( $data->address ) ?           '📍 Место : <b>'.$data->address."</b>\n" : '' );
                $text .= ( !empty( $data->time ) ?              '🕐 Время: '.$data->time."\n" : '' );
                $text .= ( !empty( $data->link ) ?              '🔗 Ссылка '.$data->link."\n" : '' );
                $text .= ( !empty( $data->content ) ? $data->content."\n" : '' );

                Bot::send( '@op_it_test', $text );

                $reminder->done = 1;
                $reminder->update();
            }


        }
    }
