<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->route('token') != null) {
            $user = User::where('token', $request->route('token'))->first();
            return ($user != null) ? $next($request) : abort(403, 'Invalid Token');
        } else {
            return $next($request);
        }
    }
}
