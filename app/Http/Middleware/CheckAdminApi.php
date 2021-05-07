<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckAdminApi
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


        //action here
        if ( (Auth::guard('api')->user()) == null || !Auth::guard('api')->user()->isAdmin() ) {
            return response()->json(
                ['errorMessage' => 'Não tem permissões suficientes para aceder a esta funcionalidade']
            );
        }


        $response = $next($request);
        return $response;

    }
}
