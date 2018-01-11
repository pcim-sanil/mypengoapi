<?php

class Utility {

    /**
     * advance print r
     * @param type $data
     * @param type $label
     */
    public static function r($data, $label = '') {
        $trace = debug_backtrace();
        $html = '';
        $html .= '<p style=background:red;padding:4px;margin:0px;>';
        $html .= ' Trace : ' . $trace[0]['file'] . ', Line :';
        $html .= '<b>' . $trace[0]['line'] . '</b>';
        $html .= '</p>';
        echo $html;
        if ($label && pathinfo($label, PATHINFO_EXTENSION) != 'txt') {
            echo $label . '<hr>';
        }
        echo '<pre style="background:#e0e0e0;padding:4px;margin:0px;clear:both;">';
        print_r($data);
        echo '</pre>';
    }

    /**
     * isset check with default value
     * @param type $dataArray
     * @param type $key
     * @param type $default
     */
    public static function getValue($dataArray, $key, $default = '') {

        if (isset($dataArray[$key])) {
            return $dataArray[$key];
        }

        return $default;
    }

    /**
     * curl get request
     * @param type $url
     * @param type $openSocketTimeout
     * @param type $readTimeout
     * @return type
     */
    public static function apiGetRequest($url, $openSocketTimeout = 30, $readTimeout = 30) {
        $headers = array(
            'Content-Type: application/json',
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $openSocketTimeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $response = array();
        $response['httpResponseMessage'] = curl_exec($ch);
        $response['httpResponseCode'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);


        curl_close($ch);

        return $response;
    }


    /**
     * curl get request
     * @param type $url
     * @param type $openSocketTimeout
     * @param type $readTimeout
     * @return type
     */
    public static function apiGetRequestOdy($url, $openSocketTimeout = 30, $readTimeout = 30) {
        $headers = array(
            'Content-Type: application/json',
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $openSocketTimeout);
        curl_setopt($ch, CURLOPT_USERPWD, "pcim_api:pcim!39bf45hL");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $response = array();
        $response['httpResponseMessage'] = curl_exec($ch);
        $response['httpResponseCode'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);


        curl_close($ch);

        return $response;
    }

    /**
     * curl post request
     * @param type $url
     * @param type $dataString
     * @param type $openSocketTimeout
     * @param type $readTimeout
     * @return type
     */
    public static function apiPostRequest($url, $dataString, $openSocketTimeout = 30, $readTimeout = 30) {

        $headers = array(
            'Content-Type: application/json',
        );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $openSocketTimeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLINFO_HEADER_OUT, false);


        $response = array();
        $response['httpResponseMessage'] = curl_exec($ch);
        $response['httpResponseCode'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        return $response;
    }

    /**
     * clean up ddi
     * @param type $requestDDI
     * @return type
     */
    public static function cleanDDI($requestDDI) {
        $DDI = trim($requestDDI, " \t\n\r\x0B");
        return "02" . substr($DDI, -8);
    }

    /**
     * get msisdn
     * @param type $requestNumber
     * @param type $countryPrefix
     * @return type
     */
    public static function getMsisdn($requestNumber, $countryPrefix) {
        $mobile = trim($requestNumber, " \t\n\r\x0B");

        switch ($countryPrefix) {
            case '65':
                $suffix = substr($mobile, -8);
                return $countryPrefix . $suffix;
            case '44':
                $suffix = substr($mobile, -10);
                return $countryPrefix . $suffix;
            case '61':
                $suffix = substr($mobile, -9);
                return $countryPrefix . $suffix;
            case '63':
                $suffix = substr($mobile, -10);
                return $countryPrefix . $suffix;
            case '353':
                $suffix = substr($mobile, -9);
                return $countryPrefix . $suffix;
            case '60':
                $suffix = substr($mobile, -9);
                return $countryPrefix . $suffix;
            case '64':
                $suffix = substr($mobile, -9);
                return $countryPrefix . $suffix;
            default:
                return $mobile;
        }
    }

    /**
     * round number
     * @param type $number
     * @param type $precision
     * @param type $seperator
     * @return type
     */
    public static function round($number = 0, $precision = 2, $seperator = '') {
        if (is_numeric($number)) {
            return number_format((float) $number, $precision, '.', $seperator);
        }

        return $number;
    }

}
