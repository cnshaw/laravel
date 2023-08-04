<?php

namespace App\Exceptions;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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

        $table_name = 'error_log_'.date('Ymd');
        if(!Schema::hasTable($table_name)) {
            Schema::create($table_name, function (Blueprint $table) {
                $table->id();
                $table->string('method');
                $table->string('path');
                $table->string('data');
                $table->string('msg');
                $table->text('trace');
                $table->timestamps();
            });
        }
        $row = [
            $request->method(),
            $request->path(),
            json_encode($request->input()),
            $e->getMessage(),
            json_encode($e->getTrace()),
            date('Y-m-d H:i:s',time()),
        ];
        DB::insert('insert into '.$table_name.' (method, path,data,msg,trace,created_at) values (?, ?, ?,?, ?,?)', $row);

        $res = ['status'=>$e->getCode(),'msg'=>$e->getMessage()];

        if(env('APP_DEBUG')){
            $res['trace'] = $e->getTrace();
        }else{
            // if code != -1  it is an  unexpected error trigger by wrong code or bad request
            if($e->getCode()!=-1) {
                $res['status'] = 500;
                $res['msg'] = 'Internal Server Error';
            }
            // check request problem
            if(str_ends_with($e->getMessage(), 'could not be found.')){
                $res['status'] = 404;
                $res['msg'] = "404 'Not Found'";
            }
        }
        return response($res);
    }
}
