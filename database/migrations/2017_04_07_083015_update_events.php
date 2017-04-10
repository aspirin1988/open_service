<?php

    use Illuminate\Support\Facades\Schema;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Database\Migrations\Migration;

    class UpdateEvents extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::table( 'events', function( Blueprint $table ) {
                $table->time( 'time' )->default('00:00:00');
            } );
        }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::table( 'events', function( Blueprint $table ) {
                $table->dropColumn( 'time' );
            } );
        }
    }
