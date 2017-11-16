<?php

/**
 * Coffees model
 */
class MypengoUnsubStatus extends BaseModel {
    /*
     * construct
     */


    public function __construct() {
        parent::__construct();

    }

    /**
     * set table name
     * @return type
     */
    public function setTableName() {
        return $this->_table = "mypengo_unsub_status";
    }

	

    /**
     * entry log of request string
     */
    public static function entryLogUnsub() {

        try {
            $reqArray = array();
           
            $reqArray['req_obj']    = json_encode($_REQUEST);
            $reqArray['res_obj']    = '';
            $reqArray['custkey']    = Utility::getValue($_REQUEST, 'custkey',date('Ymd_His'));
            $reqArray['req_number'] = Utility::getValue($_REQUEST, 'req_number');
            $reqArray['req_source'] = Utility::getValue($_REQUEST, 'req_source', 'Portal1300');        
            $reqArray['serviceid'] = Utility::getValue($_REQUEST, 'serviceid', 'serviceid');
            $reqArray['req_ddi']    = Utility::getValue($_REQUEST, 'req_ddi', '0284482842');
            $reqArray['res_status'] = '1'; // pending
           // $reqArray['created_on']   = date('Y-m-d H:i:s');
            $reqArray['req_for']    = Utility::getValue($_REQUEST, 'req_for', 'unsub');

            $mypengoUnsubStatusObj = new MypengoUnsubStatus();
        
                        return $mypengoUnsubStatusObj->create($reqArray);
            
                 
            
        } catch (Exception $exc) {
            if (ENVIRONMENT == 'DEV') {
                Utility::r($exc->getTraceAsString());
            }
        }
    }    /**
     * entry log of request string
     */
    public static function entryLog() {

        try {
            $reqArray = array();
            
            $reqArray['req_obj']    = json_encode($_REQUEST);
            $reqArray['res_obj']    = '';
            $reqArray['custkey']    = Utility::getValue($_REQUEST, 'custkey',date('Ymd_His'));
            $reqArray['req_number'] = Utility::getValue($_REQUEST, 'req_number');
            $reqArray['req_source'] = Utility::getValue($_REQUEST, 'req_source', 'Portal1300');
            $reqArray['req_ddi']    = Utility::getValue($_REQUEST, 'req_ddi', '0284482842');
            $reqArray['res_status'] = '1'; // pending
           // $reqArray['created_on']   = date('Y-m-d H:i:s');
            $reqArray['req_for']    = Utility::getValue($_REQUEST, 'req_for', 'info');

            $mypengoUnsubStatusObj = new MypengoUnsubStatus();
            return MypengoUnsubStatus::generatePacket($reqArray);
            
                 
            
        } catch (Exception $exc) {
            if (ENVIRONMENT == 'DEV') {
                Utility::r($exc->getTraceAsString());
            }
        }
    }

public static function is_multi_array( $arr ) {
    rsort( $arr );
    return isset( $arr[0] ) && is_array( $arr[0] );
}
public static function responseObject4Source($keyRequired,$apiResponse) {
/*  $keyObject      =   array(
    "voice"         => array ("Msisdn","Carrier","SubscriptionStatusDescription"),
    "portal"        =>  array ("Msisdn","TotalBilledAmount","SubscriptionId")
    );
*/

    $filterKeys             =   array();
    $response2Source        =   array();
    
    $keyObject      =   array(
        "Voice1300"             => array (
            "item2display"      =>  array("Msisdn","Carrier","SubscriptionStatusDescription","LeadDate"),
            "itemstooverride"   =>  array (
                    "Msisdn"            =>  array("0478665517" =>'au number',"0481320640" =>'excep number'),
                    "Carrier"           =>  array("Optus" =>'au Optus',"Telstra" =>'AUU Telstra')
                        )
                ),
    
        
        "Portal1300"            => array (
            "item2display"      =>  array("Msisdn","Carrier","SubscriptionStatusDescription","TotalBilledAmount","LeadDate"),
            "itemstooverride"   =>  array (
                    "Msisdn"            =>  array("0478665517" =>'au number',"0481320640" =>'excep number'),
                    "Carrier"           =>  array("Optus" =>'au Optus',"Telstra" =>'AUU Telstra')
                        )
                )
    );


    foreach($apiResponse as $elementKey => $elementVal) {
      $filterKeys[$apiResponse['SubscriptionId']] = array_intersect_key($apiResponse, array_flip($keyObject[$keyRequired]['item2display']));
       $filterKeys[$apiResponse['SubscriptionId']]['obj_count']= sizeof($apiResponse);
    //$filterKeys[$elementVal['SubscriptionId']] = array_intersect_key($elementVal, array_flip($keyObject[$keyRequired]['item2display']));
     //$filterKeys[$elementVal['SubscriptionId']]['obj_count']= sizeof($apiResponse);
    }
    
//Newly added
    //print_r($filterKeys);
    foreach($filterKeys as $tempKey => $tempVal) {
        foreach($tempVal as $k => $v) {
            if(array_key_exists($k, $keyObject[$keyRequired]['itemstooverride'])) {
            $localVarA  =   (isset($keyObject[$keyRequired]['itemstooverride'][$k][$v]) && trim($keyObject[$keyRequired]['itemstooverride'][$k][$v])!='') ? $keyObject[$keyRequired]['itemstooverride'][$k][$v] : $v;
            $tempVal[$k] = $localVarA;
                }
            }
        $response2Source[$tempKey]=$tempVal;
        }
// Newly added ended
    return $response2Source;
    }
    public static function generatePacket($reqPacket) {
        
                $localREsponse      =   array ("err_300" => array("error_message" => "No Record found for DDI ".$reqPacket['req_ddi']));
                $mypengoMappingObj  =   new MypengoMapping();
                $ddiToMap           =   "02".substr($reqPacket['req_ddi'],-8);
               
                $mypengoMapping     =   $mypengoMappingObj->fetchServiceID(['geographic_no' => $ddiToMap,'is_ddi_active' => 1]);
                      //$mypengoMapping   =   $mypengoMappingObj->fetchServiceID(['geographic_no' => $ddi, 'is_ddi_active' => 1]);
                $mypengoUnsubStatusObj = new MypengoUnsubStatus();
                if (!empty($mypengoMapping)) {
                $localREsponse=null;
                
                 $i = 0;
                foreach ($mypengoMapping as $key => $value) {
                  $logId      =   $mypengoUnsubStatusObj->create($reqPacket);
                  $i++;

                   $url = MYPENGO_BASE_URL_SUBSCRIPTION . "?serviceid=".$value['service_id']."&msisdn=".$reqPacket['req_number'];
                    $response1 = json_decode(Utility::apiGetRequest($url),true);
                   // echo "11111";print_r($response1);echo "2222";
                   // $aaa    =   json_decode($response1,true);
                  //  print_r($aaa);
                    
                    $response = MypengoUnsubStatus::responseObject4Source($reqPacket['req_source'],$response1);
                   // print_r($response);

                    $localREsponse[]  =   $response;
                       // update log
     //   echo $logId;
        MypengoUnsubStatus::updateLog($logId, $response);
            }
     //Utility::r($response);
            //end if the select result of the ddi is empty

                }else
                $logId      =   $mypengoUnsubStatusObj->create($reqPacket);
//               echo "<pre>";print_r($localREsponse);
               return json_encode($localREsponse);
    }
    /**
     *  update log
     * @param type $id
     * @param type $result
     */
    public static function updateLog($id, $response) {
        try {

            $log = array();
            $log['res_obj'] = json_encode($response);

            $log['res_status'] = !empty($response) ? '2' : '3'; // success

            $mypengoUnsubStatusObj = new MypengoUnsubStatus();
            $mypengoUnsubStatusObj->update($log, $id);
        } catch (Exception $exc) {
            if (ENVIRONMENT == 'DEV') {
                Utility::r($exc->getTraceAsString());
            }
        }
    }
	    /**
     * fetch one row by primary key
     * @param type $id
     * @return type
     */

	
	

}
