<?php

class MyPengoHelper {

    /**
     * generate packet
     * @param type $reqPacket
     * @return type
     */
    public static function getSubscriptions($ddi, $mobile, $ticketId = null, $source = 'Portal1300') {


        $DDI = Utility::cleanDDI($ddi);


        $localwithOddy = self::getOoddesseybizzData($mobile);



        $mypengoMappingObj = new MypengoMapping();

        // fetch mypengo mappings
        $mypengoMappings = $mypengoMappingObj->fetchServiceID(['geographic_no' => $DDI, 'is_ddi_active' => 1]);


        // mapping or mypengo service id not found....
        if (empty($localwithOddy) && (empty($mypengoMappings) || !is_array($mypengoMappings))) {

            return array('httpResponseMessage' => array(
                    'httpMypengoResponseCode' => 300,
                    'httpMypengoResponseMessage' => Response::getStatus(300),
                ), 'httpResponseCode' => 200);
        }

        //Hitting the Ody API
        // for every service id query mypengo api

        $localResponse = array('httpResponseCode' => '200', 'httpResponseMessage' => array());

        foreach ($mypengoMappings as $key => $value) {

            $logId = MypengoUnsubStatus::entryLog();

            $url = MYPENGO_BASE_URL_SUBSCRIPTION . "?serviceid=" . $value['service_id'] . "&msisdn=" . $mobile;

            // api call.....
            $rawResponse = Utility::apiGetRequest($url);




            $httpResponseCode = Utility::getValue($rawResponse, 'httpResponseCode');
            // response from mypengo api
            $httpResponseMessage = Utility::getValue($rawResponse, 'httpResponseMessage');


            // convert into array...
            $httpResponseMessage = json_decode($httpResponseMessage, true);


            // add extra information....
            if (is_array($httpResponseMessage)) {
                foreach ($httpResponseMessage as $k => $v) {
                    if (is_array($v)) {
                        $httpResponseMessage[$k]['mappedServiceId'] = $value['service_id'];
                        $httpResponseMessage[$k]['ticketId'] = $ticketId;
                        $httpResponseMessage[$k]['caretool'] = "kenny";
                    }
                }
            }




            $temp = array();
            $temp['httpMypengoResponseCode'] = $httpResponseCode;

            switch ($httpResponseCode) {
                case 200:
                    $temp['httpMypengoResponseMessage'] = $httpResponseMessage;
                    break;
                case 400:
                    $temp['httpMypengoResponseMessage'] = array('message' => $httpResponseMessage);
                    break;
                case 404:
                    $temp['httpMypengoResponseMessage'] = array('message' => 'Subscription Not Found');
                    break;
                default:
                    $temp = $rawResponse;
                    break;
            }


            $localResponse['httpResponseMessage'][] = $temp;

            MypengoUnsubStatus::updateLog($logId, $rawResponse);
        }


        // combine kenney with odssey
        $localResponse['httpResponseMessage'] = array_merge($localResponse['httpResponseMessage'], $localwithOddy);


        return $localResponse;
    }

    /**
     *
     * @param type $keyRequired
     * @param type $apiResponse
     * @return type
     */
    public static function responseObject4Source($keyRequired, $apiResponse) {

        return $apiResponse;

        $filterKeys = array();
        $response2Source = array();

        $keyObject = array(
            "Voice1300" => array(
                "item2display" => array("Msisdn", "Carrier", "SubscriptionStatusDescription", "LeadDate"),
                "itemstooverride" => array(
                    "Msisdn" => array("0478665517" => 'au number', "0481320640" => 'excep number'),
                    "Carrier" => array("Optus" => 'au Optus', "Telstra" => 'AUU Telstra'),
                ),
            ),
            "odddy300" => array(
                "item2display" => array("msisdn", "carrier", "active", "start_date"),
                "itemstooverride" => array(
                    "Msisdn" => array("0478665517" => 'au number', "0481320640" => 'excep number'),
                    "carrier" => array("Optus" => 'au Optus', "vodafoone" => 'AUU Telstra'),
                ),
            ),
            "Portal1300" => array(
                "item2display" => array("Msisdn", "Carrier", "SubscriptionStatusDescription", "TotalBilledAmount", "LeadDate"),
                "itemstooverride" => array(
                    "Msisdn" => array("0478665517" => 'au number', "0481320640" => 'excep number'),
                    "Carrier" => array("Optus" => 'au Optus', "Telstra" => 'AUU Telstra'),
                ),
            ),
        );

//Old and running one
        foreach ($apiResponse as $elementKey => $elementVal) {
            $filterKeys[$apiResponse['SubscriptionId']] = array_intersect_key($apiResponse, array_flip($keyObject[$keyRequired]['item2display']));
            $filterKeys[$apiResponse['SubscriptionId']]['obj_count'] = sizeof($apiResponse);
        }

        foreach ($filterKeys as $tempKey => $tempVal) {
            foreach ($tempVal as $k => $v) {
                if (array_key_exists($k, $keyObject[$keyRequired]['itemstooverride'])) {
                    $localVarA = (isset($keyObject[$keyRequired]['itemstooverride'][$k][$v]) && trim($keyObject[$keyRequired]['itemstooverride'][$k][$v]) != '') ? $keyObject[$keyRequired]['itemstooverride'][$k][$v] : $v;
                    $tempVal[$k] = $localVarA;
                }
            }
            $response2Source[$tempKey] = $tempVal;
        }

        return $response2Source;
    }

    public static function is_multi_array($arr) {
        rsort($arr);
        return isset($arr[0]) && is_array($arr[0]);
    }

    public static function getOoddesseybizzData($mobile) {

        $url1 = MYPENGO_BASE_URL_ODDESSEY_SUBSCRIPTION . "?msisdn=" . $mobile;
        $rawResponse = Utility::apiGetRequestOdy($url1);
        




        $httpResponseCode = Utility::getValue($rawResponse, 'httpResponseCode');
        $httpResponseMessage = Utility::getValue($rawResponse, 'httpResponseMessage');



        



        $filterKeys = array();

        $replaceKey = array(
            "id" => "mappedServiceId",
            "carrier" => "carrier",
            "active" => "SubscriptionStatusDescription",
            "end_date" => "UnsubscriptionDate",
            "total_billings" => "TotalBilledAmount",
            "service" => "service",
            "name" => "ServiceName",
            "country" => "country",
            "msisdn" => "Msisdn",
            "start_date" => "ActivationDate"
        );

        $response4API = json_decode($httpResponseMessage, true);
        

        $temp = array();

        $oddseyResponse = array();


        if (!empty($response4API)) {

            $temp['httpMypengoResponseCode'] = $httpResponseCode;


            // subs
            $newArray = array();

            foreach ($response4API as $elementKey => $elementVal) {

                foreach ($elementVal as $kk => $vv) {

                    if (is_array($vv)) {
                        $newArray[$replaceKey['country']] = Utility::getValue($vv, 'country');
                        $newArray[$replaceKey['name']] = Utility::getValue($vv, 'name');
                    } else if (isset($replaceKey[$kk])) {
                        $newArray[$replaceKey[$kk]] = $vv;
                    }
                }

                $newArray['mappedServiceId'] = Utility::getValue($elementVal, 'id');
                $newArray['ticketId'] = "";
                $newArray['caretool'] = "Oddsey";
                
                 $temp['httpMypengoResponseMessage'][] = $newArray;
            }



           



            $oddseyResponse[] = $temp;
        }

        return $oddseyResponse;
    }

}
