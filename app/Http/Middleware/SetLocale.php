<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Supported locales for the application.
     */
    protected array $supportedLocales = ['es', 'en'];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = null;

        // 1. Check explicit URL query parameter (?lang=es or ?locale=es)
        $queryLocale = $request->query('lang') ?? $request->query('locale');
        if ($queryLocale && in_array(strtolower($queryLocale), $this->supportedLocales, true)) {
            $locale = strtolower($queryLocale);
        }

        // 2. Check user session if available
        if (!$locale && $request->hasSession() && $request->session()->has('locale')) {
            $sessionLocale = $request->session()->get('locale');
            if (in_array(strtolower($sessionLocale), $this->supportedLocales, true)) {
                $locale = strtolower($sessionLocale);
            }
        }

        // 3. Check X-Locale or Accept-Language header (e.g. API and Mobile App)
        if (!$locale) {
            $headerLocale = $request->header('X-Locale');
            if ($headerLocale) {
                $short = strtolower(substr($headerLocale, 0, 2));
                if (in_array($short, $this->supportedLocales, true)) {
                    $locale = $short;
                }
            }
        }

        if (!$locale) {
            $acceptLanguage = $request->header('Accept-Language');
            if ($acceptLanguage) {
                $preferred = strtolower(substr($acceptLanguage, 0, 2));
                if (in_array($preferred, $this->supportedLocales, true)) {
                    $locale = $preferred;
                }
            }
        }

        // 4. Fallback to default config
        if (!$locale) {
            $locale = config('app.locale', 'es');
        }

        App::setLocale($locale);

        // Persist to session if session exists
        if ($request->hasSession()) {
            $request->session()->put('locale', $locale);
        }

        return $next($request);
    }
}
