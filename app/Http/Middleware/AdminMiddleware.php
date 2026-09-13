<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('web')->check() || Auth::guard('web')->user()->role !== 'admin') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized admin access.'], 403);
            }
            return redirect()->route('admin.login')->with('error', 'Silakan login sebagai Administrator terlebih dahulu.');
        }

        return $next($request);
    }
}
