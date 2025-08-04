<?php

namespace App\Http\Controllers;

use DB;
use Exception;
use Illuminate\Http\Request;
use App\Services\GlobalService;
use Illuminate\Support\Facades\Session;


//use JeroenNoten\LaravelAdminLte\View\Components\Tool\Datatable;


class BetoController extends Controller
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
        return view("beto", ['text1' => '']);
    }

    public function func1(Request $request) {
        session_start();


        try{
            $request->validate(['text1' => 'required|email',]);
            Session::flash('beto_OK','OK');
        }
        catch(Exception $e){
            Session::flash('beto_ERRO','ERRO');
        }



        return redirect()->back();

        //return view('beto', ['text1' => $request->text1]);
    }

    public static function func2(){
        $result = DB::select('EXEC laravel.dbo.sp_teste');
        //$resultArray = json_encode($result, true);
        $result = array_map(function ($value){
            return (array)$value;
        //    return $value;
        },$result);
        //dd($result);
        return $result;
    }

    public static function func3(){

        $result = GlobalService::populaTabelas();

        return $result;
    }
}
