<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CanAccessFile
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $file = $request->route('file');
        $file->load('archiveBox');
        if ($file->archiveBox->private) {
            if (Auth::check()) {
                $file->archiveBox->load('users');
                if ($file->archiveBox->users->contains(Auth::id())) {
                    return $next($request);
                } else {
                    abort(403, 'You don\'t have access to this file');
                }
            } else {
                abort(401, 'Please login to access this file');
            }
        } else {
            return $next($request);
        }
    }
}
