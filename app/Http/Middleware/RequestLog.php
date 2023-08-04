<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;

class RequestLog
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // $request->path();   $request->method();
		//var_dump($_SERVER['REQUEST_METHOD'],$_SERVER['REQUEST_URI'],$_POST,$_SERVER['QUERY_STRING']  );
		//echo date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']);
        $row = [
            $request->method(),
            $request->path(),
            json_encode($request->input()),
            '',
            date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME'])];
        DB::insert('insert into request_log (method, path,data,response,time) values (?, ?, ?, ?, ?)', $row);
        return $next($request);
    }
}
