<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $e)
    {
        if ($e instanceof TokenMismatchException) {
            return redirect()->route('login')
                ->with('error', 'Session expired, please login again.');
        }

        // 403 Forbidden
        if ($e instanceof HttpException && $e->getStatusCode() === 403) {
            return redirect()->route('login')
                ->with('error', 'Access denied, please login.');
        }

        return parent::render($request, $e);
    }
}
