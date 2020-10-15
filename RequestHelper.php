<?php

namespace App\Http\Model\Util;

use Illuminate\Http\Request;

class RequestHelper
{

    public static function getArgSafely(Request $request, $key, $default = null, $sanitize = null){
        $return = $request->has($key) ? $request->input($key) : $default;

        if($return == $default){
            $data = json_decode(file_get_contents('php://input'), true);
            if(is_array($data)) $return = key_exists($key, $data) ? $data[$key] : $return;
        }

        if(!empty($sanitize)){
            return filter_var($return, $sanitize);
        }

        return $return;
    }
}
