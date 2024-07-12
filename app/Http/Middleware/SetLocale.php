<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Session;
use App;
use Carbon\Carbon;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\ArrayLoader;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (config('locale.status')) {
            if (Session::has('locale') &&
                array_key_exists(Session::get('locale'), config('locale.languages'))) {
                App::setLocale(Session::get('locale'));
            } else
            {
                $userLanguages = preg_split('/[,;]/', $request->server('HTTP_ACCEPT_LANGUAGE'));
                foreach ($userLanguages as $language) {
                    foreach (config('locale.languages') as $locale => $localeLanguage) {
                        if(in_array($language, $localeLanguage)) {
                            // Set the Laravel locale
                            App::setLocale($locale);
                            // Set php setLocale
                            setlocale(LC_TIME, config('locale.languages')[$locale][1]);
                            // Set the locale configuration for Carbon
                            //Carbon::setLocale(config('locale.languages')[$locale][0]);

                            //Sets the session variable if it has RTL support
                            // if (config('locale.languages')[$locale][2]) {
                            //     session(['lang-rtl' => true]);
                            // } else {
                            //     Session::forget('lang-rtl');
                            // }

                            break 2;
                        }
                    }
                }
            }
        }
        return $next($request);
    }
}
