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

                $data->Send();

                $reminder->done = 1;
                $reminder->update();
            }


        }
    }
