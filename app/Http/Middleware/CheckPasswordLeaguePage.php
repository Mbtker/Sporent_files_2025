<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPasswordLeaguePage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
       // if($request->api_password != env("API_PASSWORD", '1mjtHFurAgeZ1ed2o'))
       // {
         //   return response()->json(['message' => 'Unauthenticated']);
       // }

        return $next($request)
            ->header('Access-Control-Allow-Origin', "*")
            ->header('Access-Control-Allow-Methods', "POST, GET, OPTIONS")
            ->header('Access-Control-Allow-Headers', "Accept, Authorization, Content-Type");
    }
}
