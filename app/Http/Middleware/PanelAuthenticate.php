<?php

namespace App\Http\Middleware;

use App\Models\AiContentTemplate;
use Closure;
use Illuminate\Support\Facades\Auth;

class PanelAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

    //   if (auth()->check() and !auth()->user()->isAdmin()) {

    //     $referralSettings = getReferralSettings();
    //     view()->share('referralSettings', $referralSettings);

    //     $aiContentTemplates = AiContentTemplate::query()->where('enable', true)->get();
    //     view()->share('aiContentTemplates', $aiContentTemplates);

    //     return $next($request);
    // }

    // // إذا في impersonation نسمح بالمرور
    // if (session()->has('impersonated')) {
    //     $referralSettings = getReferralSettings();
    //     view()->share('referralSettings', $referralSettings);

    //     $aiContentTemplates = AiContentTemplate::query()->where('enable', true)->get();
    //     view()->share('aiContentTemplates', $aiContentTemplates);

    //     return $next($request);
    // }

    // return redirect('/login');
    if (auth()->check() and !auth()->user()->isAdmin()) {

        $referralSettings = getReferralSettings();
        view()->share('referralSettings', $referralSettings);

        $aiContentTemplates = AiContentTemplate::query()->where('enable', true)->get();
        view()->share('aiContentTemplates', $aiContentTemplates);

        return $next($request);
    }

    // إذا في impersonation نسمح بالمرور
    if (session()->has('impersonated')) {
        $referralSettings = getReferralSettings();
        view()->share('referralSettings', $referralSettings);

        $aiContentTemplates = AiContentTemplate::query()->where('enable', true)->get();
        view()->share('aiContentTemplates', $aiContentTemplates);

        return $next($request);
    }

    // إذا كان أدمن وعم يحاول يوصل لـ stop-impersonate اسمحله
    if (auth()->check() and auth()->user()->isAdmin() and $request->is('*/stop-impersonate')) {
        return $next($request);
    }

    return redirect('/login');
    }
}
