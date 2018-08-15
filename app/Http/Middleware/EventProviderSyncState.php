<?php

namespace App\Http\Middleware;

use Closure;

class EventProviderSyncState
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
        if(auth()->check()){
            $access = auth()->user()->event_providers_access ?? [];

            session()->put('google-synced', false);

            foreach($access as $provider => $data){
                if($provider === 'google'){
                    session()->put('google-synced', true);
                }
            }
        }
        return $next($request);
    }
}
