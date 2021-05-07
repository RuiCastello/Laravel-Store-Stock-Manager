<?php

namespace App\Http\Controllers\API;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

// Forma alternativa de por auth web e api a funcionar ao mesmo tempo, ou isto, ou usar Auth::guard('api') em todas as calls
    public function __construct(){
        auth()->setDefaultDriver('api');
    }
}
