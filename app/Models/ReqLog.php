<?php

namespace App\Models;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;use Throwable;
class ReqLog
{
    /**
     * save req adn res log to  request_log_XXX
     */
    public static function RequestHandled(RequestHandled $event) :void
    {
        $request = $event->request;
        $response = $event->response;
        $row = [
            $request->method(),
            $request->path(),
            json_encode($request->input()),
            json_encode($response->getContent()),
            date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME'])];
        $table_name = 'request_log_'.date('Ymd');
        if(!Schema::hasTable($table_name)) {
            Schema::create($table_name, function (Blueprint $table) {
                $table->id();
                $table->string('method');
                $table->string('path');
                $table->string('data');
                $table->text('response');
                $table->timestamps();
            });
        }
        DB::insert('insert into '.$table_name.' (method, path,data,response,created_at) values (?, ?, ?, ?, ?)', $row);
    }

    /**
     * save exception log to  error_log_XXX
     */
    public static function ErrorLog($request,Throwable $e):void
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

    }
}
