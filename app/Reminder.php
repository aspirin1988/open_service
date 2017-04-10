<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    public $timestamps = true;

    protected $guarded = [];

    public function getEvent(  )
    {
        $data  = Event::where('id',$this->event_id)->first();
        return $data;
    }
}
