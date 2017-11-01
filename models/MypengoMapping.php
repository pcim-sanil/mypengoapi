<?php

/**
 * route
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
     * fetchr reviews by coffee id
     * @param type $coffeeId
     * @return type
     */
    public function fetchByCoffee($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM $this->_table WHERE auto_id = ?");
        $stmt->execute(array($id));
        return $stmt->fetchAll();
    }

}
