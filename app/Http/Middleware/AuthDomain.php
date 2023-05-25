<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthDomain
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
       
        if ($request->headers->get('origin') == '') {
            $requestHost = parse_url(trim(explode(';', $request->headers->get('user-agent'))[1]), PHP_URL_HOST);
        } else {
            $requestHost = parse_url($request->headers->get('origin'),  PHP_URL_HOST);
        }
        $allowed = DB::table('allowed_domains')->where('domain', '=', $requestHost)->first();
        if ($allowed == null) {
            return response()->json(['You are not authorized to make this request ']);
        }
        return $next($request);
    }
}
