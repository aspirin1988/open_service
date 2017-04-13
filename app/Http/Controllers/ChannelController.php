<?php

    namespace App\Http\Controllers;

    use App\Channel;
    use Illuminate\Http\Request;

    class ChannelController extends Controller
    {
        public function index()
        {

            $data = Channel::orderBy( 'name', 'DESC' )->get();

            return view( 'channel.list', [ 'that' => $this, 'channels' => $data ] );
        }

        public function add( Request $request )
        {
            $name = $request->input( 'name' );
            $link = $request->input( 'link' );
            $link = strtolower( '@'.str_replace( 'https://t.me/', '', $link ) );
            if( !empty( $name ) && !empty( $link ) ) {
                Channel::create( [ 'name' => $name, 'link' => $link, ] );
            }

            $data = Channel::orderBy( 'name', 'DESC' )->get();

            return view( 'channel.list', [ 'that' => $this, 'channels' => $data ] );

        }

        public function delete( $id )
        {
            Channel::where( 'id', $id )->delete();

            return redirect()->to( '/admin/channels/' );
        }
    }
