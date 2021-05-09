<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\Authenticatable\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\ApiResponseTrait;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException as AuthAuthenticationException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponseTrait;

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
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request,Throwable $exception)
    {
        // dd($exception);
        if($request->expectsJson()){

            //model cant find resource
            if($exception instanceof ModelNotFoundException){
                return $this->errorResponse(
                    "Can't find resource",
                    Response::HTTP_NOT_FOUND
                );
            }

            //cant find website
            if($exception instanceof NotFoundHttpException){
                return $this->errorResponse(
                    "Can't find website",
                    Response::HTTP_NOT_FOUND
                );
            }

            //cant find method
            if($exception instanceof MethodNotAllowedHttpException){
                return $this->errorResponse(
                    $exception->getMessage(),
                    Response::HTTP_METHOD_NOT_ALLOWED
                );
            }

            if($exception instanceof AuthorizationException){
                return $this->errorResponse(
                    $exception->getMessage(),
                    Response::HTTP_FORBIDDEN
                );
            }
        }
    
        //excute father's render 
        return parent::render($request,$exception);
    }

    protected function unauthenticated($request, AuthAuthenticationException $exception)
    {
        if($request->expectsJson()) {
            return $this->errorResponse(
                $exception->getMessage(),
                Response::HTTP_UNAUTHORIZED
            );
        } else {
            return redirect()->guest($exception->redirectTo()) ?? route('login');
        }
    }
}
