<?php

namespace App\Http\Middleware;

use Closure;

use App\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LoginLogs
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



        $response = $next($request);

        //action here
        $user = Auth::user();

        // dd($user);

        DB::table('logs')->insertOrIgnore(
            ['email' => $user->email, 'logged_in_time' => now()]
        );

        return $response;

    }
}
