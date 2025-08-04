<?php
// app/Http/Middleware/Google2FAMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Google2FAMiddleware
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->google2fa_secret && !$request->session()->has('2fa_authenticated')) {
            return redirect()->route('2fa.validate');
        }

        return $next($request);
    }
}
