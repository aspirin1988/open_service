<?php

    namespace App\Http\Controllers;

    use App\City;
    use Illuminate\Http\Request;

    class CityController extends Controller
    {
        public function index()
        {

            $data = City::orderBy( 'name', 'DESC' )->get();

            return view( 'city.list', [ 'that' => $this, 'cities' => $data ] );
        }

        public function add( Request $request )
        {
            $city = $request->input( 'name' );
            if( !empty( $city ) ) {
                City::create( [ 'name' => $city ] );
            }

            $data = City::orderBy( 'name', 'DESC' )->get();

            return view( 'city.list', [ 'that' => $this, 'cities' => $data ] );

        }

        public function delete( $id )
        {
            City::where('id',$id)->delete();
            return redirect()->to('/admin/cities/');
        }
    }
