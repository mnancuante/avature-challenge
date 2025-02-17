<?php

// src/Controllers/ResponseController.php

namespace App\Controllers;

class ResponseController
{
    public static function responseSuccess($message, $data = [])
    {
        $response = [
            'status' => 200,
            'status-text' => self::getHttpstatusText()[200],
            'message' => $message,
            'data' => $data
        ];

        http_response_code(200);
        echo json_encode($response);
    }

    public static function responseError($http_code = 500, $message = "Something went wrong")
    {
        http_response_code($http_code);

        echo json_encode([
            'status' => $http_code,
            'status-text' => self::getHttpstatusText()[$http_code],
            'message' => $message
        ]);
    }

    private static function getHttpstatusText()
    {
        return [
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            204 => 'No Content',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error'
        ];
    }
}
