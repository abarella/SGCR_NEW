<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Services\GlobalService;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    public function username()
    {
        return 'username';
    }

    protected function redirectTo()
    {
        return '/home'; // ou lógica baseada no usuário
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // 1. Valida via procedure
        $resultGS = GlobalService::validaAcesso($request->username, $request->password);
        
        $xmlProp = 'XML_F52E2B61-18A1-11d1-B105-00805F49916B';
        if (!$resultGS || !isset($resultGS->$xmlProp)) {
            return back()->withErrors(['username' => 'Usuário ou senha inválidos.']);
        }
        $xmlString = $resultGS->$xmlProp;
        $xml = simplexml_load_string($xmlString);
        if ($xml === false || !isset($xml['cdusuario'])) {
            return back()->withErrors(['username' => 'Usuário ou senha inválidos.']);
        }
        $cdusuario = (string) $xml['cdusuario'];
        
        


        $resultCompl = GlobalService::dadosUsuario($cdusuario);
        //dd($resultCompl->p1110_nome);

        // 2. Sincroniza/cria usuário na tabela users
        $user = \App\Models\User::where('username', $request->username)->first();
        if (!$user) {
            $user = \App\Models\User::create([
                'username'   => $request->username,
                'name'       => $resultCompl->p1110_nome ,
                'password'   => bcrypt($request->password),
                'cdusuario'  => $cdusuario,
                'email'      => $resultCompl->p1110_email,
                // Adicione outros campos obrigatórios aqui, se necessário
            ]);
        } else {
            // Atualiza o hash da senha se necessário
            if (!\Hash::check($request->password, $user->password)) {
                $user->password = bcrypt($request->password);
                $user->save();
            }
        }

        // 3. Autentica via Auth padrão
        if (\Auth::attempt(['username' => $request->username, 'password' => $request->password], $request->filled('remember'))) {
            return redirect()->intended('/home');
        }

        return back()->withErrors(['username' => 'Usuário ou senha inválidos.']);
    }

    protected function credentials(Request $request)
    {
        // Retorno padrão
        return [
            'username' => $request->username,
            'password' => $request->password
        ];
    }

    //protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
