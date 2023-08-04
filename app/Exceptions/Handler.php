<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request,Throwable $e)
    {
        $res = ['status'=>$e->getCode(),'msg'=>$e->getMessage()];

        if('0'){
            $res['trace'] = $e->getTrace();
        }else{
            // if code != -1  it is an  unexpected error trigger by wrong code
            if($e->getCode()!=-1) {
                $res['status'] = 500;
                $res['msg'] = 'Internal Server Error';
            }
            // request problem
            if(substr($e->getMessage(),-19) == 'could not be found.'){
                $res['status'] = 404;
                $res['msg'] = "404 'Not Found'";
            }
        }
        return response($res);
    }
}
