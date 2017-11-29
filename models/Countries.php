<?php

/**
 * Mypengo Mapping model
 */
class Countries extends BaseModel {

    public function __construct() {
        parent::__construct();
    }

    /**
     * set table name
     * @return type
     */
    public function setTableName() {
        return $this->_table = "countries";
    }

    /**
     * fetch one row by primary key
     * @param type $id
     * @return type
     */
    public function getCurrency($countryPrefix) {

        $stmt = $this->pdo->prepare("SELECT currency FROM $this->_table WHERE country_prefix = '$countryPrefix' limit 1");

        $stmt->execute();

        $result = $stmt->fetchColumn();

        if (!$result) {
            return '$';
        }
        
        return $result;
    }

}
