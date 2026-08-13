<?php

use App\Exceptions\ValidationFailedException;
use App\Http\JsonResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(
            at: '*',
            headers: \Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_FOR |
                \Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_HOST |
                \Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_PROTO |
                \Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_PORT
        );

        $middleware->alias([
            'defend' => \App\Http\Middleware\DefendMiddleware::class,
            'redirect.device' => \App\Http\Middleware\RedirectDeviceMiddleware::class,
            'access.log' => \App\Http\Middleware\AccessLogMiddleware::class,
            'googlebot.checked' => \App\Http\Middleware\GooglebotChecked::class,
        ]);

                $middleware->web(
            prepend: [
                \App\Http\Middleware\AccessLogMiddleware::class,
            ],
            replace: [
                \Illuminate\Cookie\Middleware\EncryptCookies::class => \App\Http\Middleware\EncryptCookies::class,
                \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class => \App\Http\Middleware\VerifyCsrfToken::class,
            ],
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // 前端统一用 fetch/jQuery ajax 提交表单，校验异常必须返回 JSON，否则前端 r.json() 解析失败报 500
        $exceptions->render(function (ValidationFailedException $e, $request) {
            return JsonResponse::make()->status(false)->statusCode(422)->message($e->getMessage())->send();
        });
    })->create();
