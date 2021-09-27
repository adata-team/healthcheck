<?php

namespace Tests;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class ExceptionsHandler extends Handler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request   $request
     * @param Exception $e
     *
     * @return Response
     * @throws Throwable
     */
    public function render($request, Throwable $e): Response
    {
        return parent::render($request, $e);
    }
}
