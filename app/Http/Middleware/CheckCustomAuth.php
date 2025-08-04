<?php
// app/Http/Middleware/Google2FAMiddleware.php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Session;


class CheckCustomAuth
{
public function handle($request, Closure $next)
{
    if (!Session::has('cdusuario')) {
        return redirect('/login');
    }
    return $next($request);
}
}