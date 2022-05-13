<?php

namespace App\Exceptions;

use App\Notifications\SlackNotification;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Notification;
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
        $exception_msg = $exception->getMessage();
        if (env('APP_ENV', 'prod') !== 'local' && $exception_msg !== null) {

            // message
            $error_msg = "Error: " .$exception_msg . PHP_EOL;
            $error_msg .= "File: " .$exception->getFile() . PHP_EOL;
            $error_msg .= "Line: " .$exception->getLine() . PHP_EOL;

            // if 500
            $error_response = $this->prepareJsonResponse($request, $exception);
            if(strpos($error_response, 'HTTP/1.0 500 Internal Server Error') !== false){

                // get array
                $error_arr = $this->convertExceptionToArray($exception);

                // add trace
                $error_msg .= "Trace: " . PHP_EOL;
                if(key_exists('trace', $error_arr)){
                    foreach ($error_arr['trace'] AS $trace){
                        if(key_exists('file', $trace)) {
                            $error_msg .= $trace['file'] . PHP_EOL;
                        }
                    }
                }

                Notification::route('slack', env('SLACK_HOOK'))->notify(new SlackNotification($error_msg));
            }
        }

        // don't show full log to user in prod
        if(env('APP_ENV', 'prod') === 'prod'){
            return response()->view('error.server', [], 500);
        }

        return parent::render($request, $exception);
    }
}
