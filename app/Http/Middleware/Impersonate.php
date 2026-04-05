<?php


namespace App\Http\Middleware;


use Closure;
use Illuminate\Support\Facades\Auth;


class Impersonate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
       \Log::info('Impersonate Middleware', [
            'impersonated' => session()->get('impersonated'),
            'impersonator_id' => session()->get('impersonator_id'),
            'all_session' => session()->all(),
            'url' => $request->url(),
        ]);


     if (session()->has('impersonated')) {
            $userId = session()->get('impersonated');
            $user = \App\User::find($userId);

            if ($user) {
                if ($user->isAdmin()) {
                    Auth::guard()->setUser($user);
                } else {
                    Auth::onceUsingId($userId);
                }
            }
        }

        return $next($request);
    }
}



