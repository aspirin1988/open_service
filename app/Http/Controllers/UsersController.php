<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index(  )
    {
        return view('user.list',['that'=>$this]);
    }

    public function add(  )
    {
        return view('user.add',['that'=>$this]);
    }
}
