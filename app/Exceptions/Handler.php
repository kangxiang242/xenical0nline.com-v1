<?php

namespace App\Exceptions;

use App\Http\JsonResponse;
use App\Models\Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
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
     * @throws \Throwable
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

        /*try {

            Exception::create([
                'ip'=>$request->header('cf-connecting-ip',$request->ip()),
                'ip_country'=>$request->header('cf-ipcountry'),
                'status_code'=>$exception->getCode(),
                'message'=>$exception->getMessage(),
                'uri'=>$request->getUri(),
                'method'=>$request->method(),
                'referer'=>$request->header('referer'),
                'user_agent'=>$request->userAgent(),
                'parameters'=>$request->all(),
                'headers'=>$request->header(),
                'trace'=>$exception->getTrace()
            ]);
        }catch (\Exception $exception){

        }*/

        if($exception instanceof ValidationFailedException){
            if(request()->ajax()){
                return JsonResponse::make()->status(false)->statusCode(422)->message($exception->getMessage())->send();
            }
        }

        return parent::render($request, $exception);
    }
}
