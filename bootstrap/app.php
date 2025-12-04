<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
        $middleware->alias([
            'moderator' => \App\Http\Middleware\ModeratorMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (AuthenticationException $e, $request) {
            return $request->expectsJson()
                ? response()->json([
                    'message' => 'Недостаточно прав',
                ], Response::HTTP_UNAUTHORIZED)
                : redirect()->guest($e->redirectTo() ?? route('login'));
        });

        $exceptions->renderable(function (NotFoundHttpException|ModelNotFoundException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Запрашиваемая страница не существует.',
                ], Response::HTTP_NOT_FOUND);
            }
        });

        $exceptions->renderable(function (ValidationException $e, $request) {
            if ($request->expectsJson()) {
                $errors = $e->errors();
                $response = [
                    'message' => 'Переданные данные не корректны.',
                    'errors' => $errors,
                ];
                return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        });

        $exceptions->renderable(function (Throwable $e, $request) {
            if ($request->expectsJson()) {
                return (new \App\Http\Responses\FailResponse(
                    $e->getMessage(),
                    $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR,
                    $e
                ))->toResponse($request);
            }
        });

        $exceptions->reportable(function (Throwable $e) {
        });
    })->create();
