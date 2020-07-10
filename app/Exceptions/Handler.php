<?php

namespace App\Exceptions;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use TeamTNT\TNTSearch\Exceptions\IndexNotFoundException;
use Throwable;

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
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ModelNotFoundException) {
            return response()->json(['error' => 'Recurso no encontrado'], 404);
        }
        if ($exception instanceof QueryException) {
            return response()->json(['error' => 'Error de consulta: ' . $exception->getMessage()], 400);
        }
        if ($exception instanceof HttpException || $exception instanceof BindingResolutionException) {
            return response()->json(['error' => 'La ruta especificada no existe'], 404);
        }
        if ($exception instanceof ValidationException) {
            return response()->json(['error' => $exception->validator->errors()], 400);
        }
        //Si el indice no existe
        if ($exception instanceof IndexNotFoundException) {
            return response()->json(['error' => 'No existe índice de registros'], 404);
        }

        return parent::render($request, $exception);
    }
}
