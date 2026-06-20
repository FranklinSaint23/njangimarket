<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                return redirect($this->redirectTo($user->role));
            }
        }

        return $next($request);
    }

    protected function redirectTo(string $role): string
    {
        return match($role) {
            'admin'   => '/admin/dashboard',
            'vendeur' => '/vendeur/dashboard',
            'livreur' => '/livreur/dashboard',
            default   => '/dashboard',
        };
    }
}
