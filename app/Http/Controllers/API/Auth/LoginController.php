<?php

namespace App\Http\Controllers\API\Auth;

//copiámos o controller em App\Http\Controllers\ e fizemos uma copia modificada em App\Http\Controllers\API\ que muda o auth driver para API. Assim conseguimos ter web e api a funcionar com dois tipos de auth diferentes, web com o sistema normal de sessions do laravel e api com jwt.
use App\Http\Controllers\API\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    //
    public function login(Request $request){
        $creds = $request->only(['email', 'password']);

        $token = auth()->attempt($creds);

        return response()->json(['token' => $token]);
    }
}
