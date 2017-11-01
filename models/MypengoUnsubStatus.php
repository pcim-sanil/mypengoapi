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
     * entry log
     */
    public static function entryLog() {

        try {
            $log = array();
            $log['req_obj'] = json_encode($_REQUEST);
            $log['res_obj'] = '';
            $log['custkey'] = Utility::getValue($_REQUEST, 'custkey');
            $log['req_number'] = Utility::getValue($_REQUEST, 'req_number');
            $log['req_source'] = Utility::getValue($_REQUEST, 'req_source', 'IVR');
            $log['res_status'] = '1'; // pending
            $log['created_on'] = date('Y-m-d H:i:s');
            $log['req_for'] = Utility::getValue($_REQUEST, 'req_for', 'info');

            $mypengoUnsubStatusObj = new MypengoUnsubStatus();
            return $mypengoUnsubStatusObj->create($log);
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
