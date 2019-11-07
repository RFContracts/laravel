<?php

namespace App\Http\Middleware;

use App;
use Closure;

class CheckAuthorization
{
    /**
     * @param $request
     * @param Closure $next
     * @return mixed|void
     */
    public function handle($request, Closure $next)
    {
        if ($request->header('Authorization') == null) {
            return abort(401, 'Unauthorized');
        }
        $response = explode('Bearer', $request->header('Authorization'));

        if (count($response) != 2) {
            return abort(401, 'Unauthorized');
        }
        $token = trim($response[1]);
        if ($token != config('config.key')) {
            return abort(401, 'Unauthorized');
        }

        return $next($request);
    }
}
