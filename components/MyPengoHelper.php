<?php

class MyPengoHelper {

    /**
     * generate packet
     * @param type $reqPacket
     * @return type
     */
    public static function getSubscriptions($ddi, $mobile, $ticketId = NULL, $source = 'Portal1300') {

        $DDI = Utility::cleanDDI($ddi);

        $mypengoMappingObj = new MypengoMapping();

        // fetch mypengo mappings
        $mypengoMappings = $mypengoMappingObj->fetchServiceID(['geographic_no' => $DDI, 'is_ddi_active' => 1]);

        // mapping or mypengo service id not found....
        if (empty($mypengoMappings) || !is_array($mypengoMappings)) {
            return array('httpResponseMessage' => array(
                    'httpMypengoResponseCode' => 300,
                    'httpMypengoResponseMessage' => Response::getStatus(300),
                ), 'httpResponseCode' => 200);
        }


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



            $temp = array();
            $temp['httpMypengoResponseCode'] = $httpResponseCode;

            switch ($httpResponseCode) {
                case 200:
                    $temp['httpMypengoResponseMessage'] = self::responseObject4Source($source, json_decode($httpResponseMessage, true));
                    break;
                case 400:
                    $temp['httpMypengoResponseMessage'] = array('message' => json_decode($httpResponseMessage, true));
                    break;
                case 404:
                    $temp['httpMypengoResponseMessage'] = array('message' => 'Subscription Not Found');
                    break;
                default:
                    $temp = $rawResponse;
                    break;
            }


            // embed service id info
            if (is_array($temp['httpMypengoResponseMessage'])) {
                $temp['httpMypengoResponseMessage']['mappedServiceId'] = $value['service_id'];
                $temp['httpMypengoResponseMessage']['ticketId'] = $ticketId;
            }



            $localResponse['httpResponseMessage'][] = $temp;

            MypengoUnsubStatus::updateLog($logId, $rawResponse);
        }


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

        //return;
        $filterKeys = array();
        $response2Source = array();

        $keyObject = array(
            "Voice1300" => array(
                "item2display" => array("Msisdn", "Carrier", "SubscriptionStatusDescription", "LeadDate"),
                "itemstooverride" => array(
                    "Msisdn" => array("0478665517" => 'au number', "0481320640" => 'excep number'),
                    "Carrier" => array("Optus" => 'au Optus', "Telstra" => 'AUU Telstra')
                )
            ),
            "Portal1300" => array(
                "item2display" => array("Msisdn", "Carrier", "SubscriptionStatusDescription", "TotalBilledAmount", "LeadDate"),
                "itemstooverride" => array(
                    "Msisdn" => array("0478665517" => 'au number', "0481320640" => 'excep number'),
                    "Carrier" => array("Optus" => 'au Optus', "Telstra" => 'AUU Telstra')
                )
            )
        );


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

}
