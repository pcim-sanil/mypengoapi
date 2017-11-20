<?php

/**
 * Mypengo Mapping model
 */
class MypengoMapping extends BaseModel {

    public function __construct() {
        parent::__construct();
    }

    /**
     * set table name
     * @return type
     */
    public function setTableName() {
        return $this->_table = "mypengo_mapping";
    }

    /**
     * fetch one row by primary key
     * @param type $id
     * @return type
     */
    public function fetchServiceID($params) {

        $conditions = array();
        $values = array();

        foreach ($params as $key => $value) {
            $conditions[] = " $key = :$key ";
            $values[":$key"] = $value;
        }

        if (empty($conditions) || empty($values)) {
            return array();
        }

        $conditionsString = implode(" AND ", $conditions);

        $stmt = $this->pdo->prepare("SELECT mm.service_id FROM $this->_table AS mm LEFT JOIN sms_services AS ss  ON mm.sms_service_id=ss.id  WHERE $conditionsString");

        $stmt->execute($values);

        return $stmt->fetchAll();
    }

}