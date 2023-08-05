<?php

namespace App\Http\Controllers\api;

use http\Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use function PHPUnit\Framework\throwException;

class TestController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /* a. GET method to return the request content */
    public function a(Request $request) :string
    {
        $data = $request->query();
        $res = ['status'=>1,'data'=>$data];
        return json_encode($res);
    }

    /* b. POST method to return the request content */
    public function b(Request $request) :string
    {
        $data = $request->input();
        $res = ['status'=>1,'data'=>$data];
        return json_encode($res);
    }

    /* c. GET method, which throws an expected error, such as a request field format error */
    public function c(Request $request) :string
    {
        //throws an expected error use Exception code -1
        throw new \Exception(' format error ',-1);
    }

    /* d. GET method, which throws an unexpected error */
    public function d(Request $request) :string
    {
        $a = [];
        // this will trigger an  unexpected error;
        return json_encode($a['a']);
    }

    /* e. GET method, which use the url query 's' for the logical test below */
    public function e(Request $request) :string
    {
        $res = ['status'=>1,'data'=>false];
        $s = $request->query('s');
        $length = strlen($s);
        if($length<1||$length>10000){
            $res['msg'] = " s length must >=1 and <=10000 ";
            return json_encode($res);
        }
        if($length/2==1){
            return json_encode($res);
        }
        $close_char = '';
        for($i=0;$i<$length;$i++) {
            $char = $s[$i];
            if(!in_array($char,['(',')','[',']','{','}'])) {
                $res['msg'] = "'s' consists of parentheses only '()[]{}'";
                break;
            }
            if($close_char =='') {
                if ($char == '(') $close_char = ')';
                if ($char == '{') $close_char = '}';
                if ($char == '[') $close_char = ']';
            }else{
                if($char == $close_char){
                    $res['data'] =true;
                    $close_char = '';
                }else{
                    $res['data'] =false;
                    break;
                }
            }
        }
        if($close_char!='') $res['data'] =false;
        return json_encode($res);
    }
}

