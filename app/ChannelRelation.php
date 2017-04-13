<?php

    namespace App;

    use Illuminate\Database\Eloquent\Model;

    class ChannelRelation extends Model
    {
        public $timestamps = true;

        protected $guarded = [];

        public function getChannel()
        {
            $data = Channel::where( 'id', $this->channel_id )->first();

            $this->channel = $data;

            return $data;
        }

    }
