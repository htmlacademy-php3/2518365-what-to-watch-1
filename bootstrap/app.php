<?php

use App\Http\Middleware\ModeratorMiddleware;
use App\Http\Responses\FailResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
        $middleware->alias([
            'moderator' => ModeratorMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Запрос требует аутентификации',
                ], Response::HTTP_UNAUTHORIZED);
            }

            return redirect()->guest($e->redirectTo() ?? route('login'));
        });
        $exceptions->render(function (NotFoundHttpException|ModelNotFoundException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Запрашиваемая страница не существует',
                ], Response::HTTP_NOT_FOUND);
            }
        });
        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->expectsJson()) {
                $errors = $e->errors();
                $response = [
                    'message' => 'Переданные данные не корректны',
                    'errors' => $errors,
                ];
                return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        });
        $exceptions->render(function (Throwable $e, $request) {
            if ($request->expectsJson()) {
                return (new FailResponse(
                    $e->getMessage(),
                    $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR,
                    $e
                ))->toResponse($request);
            }
        });
    })->create();
