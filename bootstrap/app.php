<?php

use App\Http\Middleware\CheckUserRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request; // <<<--- បន្ថែមបន្ទាត់នេះ
use Symfony\Component\HttpKernel\Exception\HttpException; // <<<--- បន្ថែមបន្ទាត់នេះ

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => CheckUserRole::class,
        ]);

        // Register global middleware for security headers
        $middleware->append(\App\Http\Middleware\AddSecurityHeaders::class);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        // បន្ថែម Logic Redirect នៅទីនេះ
        $exceptions->render(function (HttpException $e, Request $request) {
            // បើជួប Error 419 (Session Expired) ឬ 403 (No Permission)
            if (in_array($e->getStatusCode(), [419, 403])) {
                return redirect()->route('login')
                    ->with('error', 'Session របស់អ្នកហួសកំណត់ ឬអ្នកមិនមានសិទ្ធិចូលទំព័រនេះទេ។ សូមចូលប្រព័ន្ធម្ដងទៀត!');
            }
        });
    })->create();
