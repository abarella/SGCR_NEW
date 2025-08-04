<?php

namespace App\Http\Controllers\UserController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\GlobalService;

class adminUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view("admin_users\index");
    }

    public static function retornaUsuariosCadastrados(){

        $result = GlobalService::retornaUsuariosCadastrados();

        return $result;
    }

}
