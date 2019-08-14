<?php

namespace App\Http\Middleware;

use Closure;

class CorsMiddleware
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
        // Pre-Middleware Action

        $response = $next($request)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Access-Control-Allow-Headers Origin, Accept ,Content-Type, Authorization, X-Requested-With, Access-Control-Request-Method, Access-Control-Request-Headers');

        // $IlluminateResponse = 'Illuminate\Http\Response';
        // $SymfonyResponse = 'Symfony\Component\HttpFoundation\Response';
        // $headers = [
        //     'Access-Control-Allow-Origin', '*',
        //     'Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS',
        //     'Access-Control-Allow-Headers', 'Access-Control-Allow-Headers Origin, Accept ,Content-Type, Authorization, X-Requested-With, Access-Control-Request-Method, Access-Control-Request-Headers'
        // ];

        // if($response instanceof $IlluminateResponse) {
        //     foreach ($headers as $key => $value) {
        //         $response->header($key, $value);
        //     }
        //     return $response;
        // }

        // if($response instanceof $SymfonyResponse) {
        //     foreach ($headers as $key => $value) {
        //         $response->headers->set($key, $value);
        //     }
        //     return $response;
        // }

        // Post-Middleware Action

        return $response;
    }
}
