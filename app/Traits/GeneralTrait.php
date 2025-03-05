<?php

namespace App\Traits;

trait GeneralTrait
{
    public function getCurrentLang()
    {
        return app()->getLocale();
    }

    public function returnError($ErrorCode, $msg)
    {
        return response()->json([
            'status' => false,
            'ErrorCode' => $ErrorCode,
            'msg' => $msg
        ]);
    }

    public function returnLoginError($ErrorCode, $msg, $AuthCode)
    {
        return response()->json([
            'status' => false,
            'ErrorCode' => $ErrorCode,
            'msg' => $msg,
            'AuthCode' => $AuthCode
        ]);
    }

    public function returnLoginDate($key, $value, $AuthCode, $msg = "")
    {
        return response()->json([
            'status' => true,
            'ErrorCode' => "0",
            'AuthCode' => $AuthCode,
            $key => $value
        ]);
    }

    public function returnSuccessMessage($msg = "")
    {
        return response()->json([
            'status' => true,
            'ErrorCode' => "0",
            'msg' => $msg
        ]);
    }

    public function returnMessage($Status, $Code, $msg)
    {
        return response()->json([
            'status' => $Status,
            'code' => $Code,
            'msg' => $msg
        ]);
    }

    public function returnDate($key, $value, $msg = "")
    {
        return response()->json([
            'status' => true,
            'ErrorCode' => "0",
            $key => $value
        ]);
    }

}
