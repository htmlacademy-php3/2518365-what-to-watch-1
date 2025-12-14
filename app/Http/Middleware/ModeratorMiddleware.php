<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * @psalm-suppress UnusedClass
 */
class ModeratorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return response()->json([
                'message' => 'Требуется авторизация'
            ], Response::HTTP_UNAUTHORIZED);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        /** @psalm-suppress UndefinedMethod */
        if ($user->isModerator()) {
            return $next($request);
        }

        return response()->json([
            'message' => 'Недостаточно прав'
        ], Response::HTTP_FORBIDDEN);
    }
}
