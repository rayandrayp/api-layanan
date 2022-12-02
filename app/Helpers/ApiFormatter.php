<?php

namespace App\Helpers;

class ApiFormatter
{
    protected static $response = [
        'metaData' => [
            'code' => null,
            'message' => null,
        ],
        'result' => null
    ];

    public static function createAPI($code = null, $message = null, $data = null)
    {
        self::$response['metaData']['code'] = $code;
        self::$response['metaData']['message'] = $message;
        self::$response['result'] = $data;

        return response()->json(self::$response, self::$response['metaData']['code']);
    }
}
