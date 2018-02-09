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
     * get subscriptions from mypengo api
     * @return type
     */
    public static function entryLog($url = "") {
        try {
            $mypengoUnsubStatusObj = new MypengoUnsubStatus();

            $reqArray = array();
            
            $reqArray['req_obj'] = json_encode($_REQUEST);
            $reqArray['res_obj'] = $url;
            $reqArray['ticket_id'] = Utility::getValue($_REQUEST, 'ticket_id');
            $reqArray['custkey'] = Utility::getValue($_REQUEST, 'custkey', date('Ymd_His'));
            $reqArray['req_number'] = Utility::getValue($_REQUEST, 'req_number');
            $reqArray['req_source'] = Utility::getValue($_REQUEST, 'req_source', 'Portal1300');
            $reqArray['req_ddi'] = Utility::getValue($_REQUEST, 'req_ddi', '');
            $reqArray['res_status'] = '1';
            $reqArray['created_on'] = date('Y-m-d H:i:s');
            $reqArray['req_for'] = Utility::getValue($_REQUEST, 'req_for', 'info');


            return $mypengoUnsubStatusObj->create($reqArray);
        } catch (Exception $exc) {
            if (ENVIRONMENT == 'DEV') {
                Utility::r($exc->getTraceAsString());
            }
        }
    }

    /**
     *  update log
     * @param type $id
     * @param type $result
     */
    public static function updateLog($id, $response) {
        
        return;
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

}
