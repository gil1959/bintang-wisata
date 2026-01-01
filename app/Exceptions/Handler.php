<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Request;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
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
    // redirect 404 frontend ke homepage (SEO safe)
    $this->renderable(function (NotFoundHttpException $e, Request $request) {

        // JANGAN ganggu admin, api, asset
        if (
            $request->is('admin/*') ||
            $request->is('api/*') ||
            $request->expectsJson()
        ) {
            return null; // biarin Laravel handle normal
        }

        return redirect('/')
            ->setStatusCode(302); // TEMPORARY (AMAN SEO)
    });
}

}
