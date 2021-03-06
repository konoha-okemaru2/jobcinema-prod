<?php

namespace App\Exceptions;

use Exception;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        return parent::render($request, $exception);
    }

    /**
     * 共通エラーページ
     */
    protected function renderHttpException(\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e)
    {
        $status = $e->getStatusCode();
        if (auth('employer')->check()) {
            return response()->view("errors.employer.common", ['exception' => $e], $status);
        }
        return response()->view("errors.common", ['exception' => $e], $status);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $exception->getMessage()], 401);
        }

        if (in_array('employer', $exception->guards())) {
            return redirect()->guest(route('employer.login'));
        }
        if (in_array('admin', $exception->guards())) {
            return redirect()->guest(route('admin.login'));
        }

        return redirect()->guest(route('seekder.login'));
    }
}
