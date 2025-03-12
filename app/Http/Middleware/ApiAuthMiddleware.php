<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;


class ApiAuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization');
        $authenticate = true;

        if (!$token || !($user = User::where('token', $token)->first())) {
            $authenticate = false;
        } else {
            Auth::login($user);
        }

        if ($authenticate) {
            return $next($request);
        } else {
            return response()->json(["errors" => ["message" => ["unauthorized"]]], 401);
        }
    }
}
