<?php

namespace App\Http\Controllers;

use App\Services\GlobalService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        //$username = Auth::user()->username;
        
       // $result = GlobalService::validaAcesso(Auth::user()->username);

        //try{
            //if ($result[0]['usuario'] != null ){
                return view('home');
            //}
            //else {
            //    Auth::logout();
            //    return view('auth/login');
           // }
        //}
        //catch(\Exception $e){
          //  Auth::logout();
           // return view('auth/login');

        //}


    }
}
