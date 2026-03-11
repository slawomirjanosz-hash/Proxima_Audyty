<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = array_keys(config('localization.supported_locales', ['pl' => 'PL', 'en' => 'EN']));
        $defaultLocale = config('localization.default_locale', config('app.locale', 'pl'));
        $locale = $request->session()->get('locale', $defaultLocale);

        if (! in_array($locale, $supportedLocales, true)) {
            $locale = $defaultLocale;
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
