<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckAdmin
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
        if ( ! Auth::user()->isAdmin() ) {
            return Redirect()->back()->with(['errorMessage' => 'Não tem permissões suficientes para aceder a esta funcionalidade']);
        }


        $response = $next($request);
        return $response;

    }
}
