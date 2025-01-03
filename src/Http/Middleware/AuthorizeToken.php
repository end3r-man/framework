<?php

namespace Laracord\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class AuthorizeToken
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken() ?? $request->get('token');

        if (! $token) {
            return response()->json(['message' => 'You must specify a token.'], 401);
        }

        $token = PersonalAccessToken::findToken($token);

        if (! $token || ! $token->can('http')) {
            return response()->json(['message' => 'You are not authorized.'], 403);
        }

        $user = $token->tokenable;

        $request->merge(['user' => $user]);

        return $next($request);
    }
}
