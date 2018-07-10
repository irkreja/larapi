<?php

namespace App\Exceptions;

use Exception;
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
    public function report(Exception $exception)
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
    public function render($request, Exception $exception)
    {

        // 404 Not found / Custom validation structure
        if ($exception instanceof \Illuminate\Validation\ValidationException)
        {
            return $request->expectsJson()
            ? response()->json([
                'data' => [
                    'message' => $exception->getMessage(),
                    'errors' => $exception->errors()
                ]
            ], 422)
            : redirect()->guest(route('login'));
        }
        // 404 Not found / No Model/Post Found
        if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException)
        {
            return response()->json([
                'data' => [
                    'message' => 'Resource not found.'
                ]
            ], 404);
        }

        // 404 Not found / No route found
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException)
        {
            return response(null, 404);
        }

        //401 Unauth­orized // not owner
        if ($exception instanceof  \Illuminate\Auth\AuthenticationException) {
            // return response()->view('errors.custom', [], 500);
            return $request->expectsJson()
                    ? response()->json([
                        'data' => [
                            'message' => $exception->getMessage()
                        ]
                    ], 401)
                    : redirect()->guest(route('login'));
        }

        //403 Forbidden // didn't pass bearer token/token invalid
        if ($exception instanceof  \Illuminate\Auth\Access\AuthorizationException) {
            // return response()->view('errors.custom', [], 500);
            return $request->expectsJson()
                    ? response()->json([
                        'data' => [
                            'message' => $exception->getMessage()
                        ]
                    ], 403)
                    : redirect()->guest(route('login'));
        }
        return parent::render($request, $exception);

        // return response($exception);
    }
}
