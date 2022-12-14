<?php

namespace App\Exceptions;

use App\Api\ExceptionReport;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     *渲染异常为 HTTP 响应。
     *
     * @param  Request  $request
     * @param  Throwable  $e
     * @return Response
     * @throws Throwable
     */
    public function render($request, Throwable $e)
    {
        //ajax请求我们才捕捉异常
        if ($request->ajax()) {
            // 将方法拦截到自己的ExceptionReport
            $reporter = ExceptionReport::make($e);
            if ($reporter->shouldReturn()) {
                return $reporter->report();
            }
            if (env('APP_DEBUG')) {
                //开发环境，则显示详细错误信息
                return parent::render($request, $e);
            } else {
                //线上环境,未知错误，则显示500
                return $reporter->prodReport();
            }
        }
        return parent::render($request, $e);
    }
}
