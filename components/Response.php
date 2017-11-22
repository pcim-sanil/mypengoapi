<?php

class Response {

    /**
     * get status
     * @param type $code
     * @return type
     */
    private static function getStatus($code) {
        $status = array(
            200 => 'OK',
            300 => 'Mapping Not Found',
            400 => 'Invalid Service ID',
            404 => 'Subscription Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        );

        return ($status[$code]) ? $status[$code] : $status[500];
    }

    /**
     * send json response
     * @param type $data
     * @param type $status
     */
    public static function sendJSON($data, $status = 200) {
        header("HTTP/1.1 " . $status . " " . self::getStatus($status));
        header('Content-type: application/json');
        echo json_encode($data);
        exit();
    }

}
