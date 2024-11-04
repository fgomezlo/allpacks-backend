<?php

class ConsolidacionItemDAOImpl extends mysql {
    
    private $_COLUMNS = "`allpack_consolidar_item`.`id_consolida_item`, "
            . "`allpack_consolidar_item`.`carrier_consolidar`, `allpack_consolidar_item`.`tracking_consolidar`, "
            . "`allpack_consolidar_item`.`des_consolidar`, `allpack_consolidar_item`.`id_consolidar`, "
            . "`allpack_consolidar_item`.`status_consolidar_item`, `allpack_consolidar_item`.`warehouse`, "
            . "`allpack_consolidar_item`.`nota`, `allpack_consolidar_item`.`valor_consolidar`, "
            . "`allpack_consolidar_item`.`url_factura`";

    private function createObj($value) {

        $obj = new ConsolidacionItem();
        $obj->setId($value["id_consolida_item"]);
        $obj->setCarrier($value["carrier_consolidar"]);
        $obj->setTracking($value["tracking_consolidar"]);
        $obj->setDes($value["des_consolidar"]);
        $obj->setEstatus($value["status_consolidar_item"]);
        $obj->setWarehouse($value["warehouse"]);
        $obj->setNota($value["nota"]);
        $obj->setValor($value["valor_consolidar"]);
        $obj->setUrlfactura($value["url_factura"]);
        $obj->setIdconsolidacion($value["id_consolidar"]);

        return $obj;
    }

    /**
     * Save or update user on database 
     * @param ConsolidacionItem $obj
     * @return \ConsolidacionItem
     */
    public function saveObj($obj) {
        
        $id_consolida_item = $obj->getId();
        $carrier_consolidar = $obj->getCarrier();
        $tracking_consolidar = $obj->getTracking();
        $des_consolidar = $obj->getDes();
        $id_consolidar = $obj->getIdconsolidacion();
        $status_consolidar_item = $obj->getEstatus();
        $warehouse = $obj->getWarehouse();
        $nota = $obj->getNota();
        $valor_consolidar = $obj->getValor();
        $url_factura = $obj->getUrlfactura();
        
        if ($id_consolida_item > 0) {

            //update object
            $query = "UPDATE `allpack_consolidar_item` "
                    . "SET `carrier_consolidar` = ?, `tracking_consolidar` = ?, `des_consolidar` = ?, "
                    . "`id_consolidar` = ? , `status_consolidar_item` = ?, `warehouse` = ?, " 
                    . "`nota` = ? , `valor_consolidar` = ? , `url_factura` = ? "
                    . "WHERE id_consolida_item = ?";

            $stmt = $this->createPreparedStatement($query);

            mysqli_stmt_bind_param($stmt, "sssiissdsi", $carrier_consolidar, 
                    $tracking_consolidar, $des_consolidar, $id_consolidar, 
                    $status_consolidar_item, $warehouse, $nota, 
                    $valor_consolidar, $url_factura, $id_consolida_item);

            $this->executeUpdateOrDeletePreparedStatement($stmt, $query);
        } else {

            //new object
            $query = "INSERT INTO `allpack_consolidar_item` ("
                    . "`carrier_consolidar`, `tracking_consolidar`, `des_consolidar`, "
                    . "`id_consolidar`, `status_consolidar_item`, `warehouse`, "
                    . "`nota`, `valor_consolidar`, `url_factura`) "
                    . "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?) ";
            
            $stmt = $this->createPreparedStatement($query);
            mysqli_stmt_bind_param($stmt, "sssiissds", $carrier_consolidar, 
                    $tracking_consolidar, $des_consolidar, $id_consolidar, 
                    $status_consolidar_item, $warehouse, $nota, 
                    $valor_consolidar, $url_factura);

            $obj->setId($this->executeInsertWithAutoIncrementPreparedStatement($stmt, $query));
        }

        return $obj;
    }

    public function delObj(Customer $obj) {
        
        
        $id = $obj->getId();
        try {
            $query = "delete from `allpack_consolidar_item` where id_consolida_item = ? ";
            $stmt = $this->createPreparedStatement($query);
            mysqli_stmt_bind_param($stmt, "i", $id);
            $this->executeUpdateOrDeletePreparedStatement($stmt, $query);
        } catch (Exception $e) {
            return false;
        }
        
        return true;
    }
    
    public function filterAllObj($arrayfilter, $where = false) {

        $filter = "";

        if ($arrayfilter == null || !is_array($arrayfilter))
            return $filter;

        if (isset($arrayfilter["status"]) && $arrayfilter["status"] != null) {
            $filter .= ( $where ? " AND " : " WHERE " ) . " `allpack_consolidar_item`.status_consolidar_item = '" . $arrayfilter["status"] . "'";
            $where = true;
        }
        
        if (isset($arrayfilter["id"]) && $arrayfilter["id"] != null) {
            $filter .= ( $where ? " AND " : " WHERE " ) . " `allpack_consolidar_item`.id_consolida_item in (" . $arrayfilter["id"] . ")";
            $where = true;
        }
        
        if (isset($arrayfilter["consolidacionid"]) && $arrayfilter["consolidacionid"] != null) {
            $filter .= ( $where ? " AND " : " WHERE " ) . " `allpack_consolidar_item`.id_consolidar in (" . $arrayfilter["consolidacionid"] . ")";
            $where = true;
        }

        return $filter;
    }

    public function getAllObjs($filter = null) {
        
        $condition = $this->filterAllObj($filter, false);

        $query = " SELECT " . $this->_COLUMNS
                . " FROM `allpack_consolidar_item` "
                . $condition 
                . "ORDER BY `allpack_consolidar_item`.id_consolida_item ";

        // conditional to paginateion 
        if ($filter != null && isset($filter["show"]) && isset($filter["offset"])) {
            $query .= " LIMIT " . $filter["offset"] . "," . $filter["show"];
        }

        //check if filter contains limit of products to show
        if ($filter != null && isset($filter["limit"])) {
            $query .= " LIMIT " . $filter["limit"] . " ";
        }
        
        $result = $this->executeQuery($query);

        if ($result == null) { return null ; }

        $arrayRol = [];
        for ($i = 0; $i < count($result); $i++) {

            $datos = $this->createObj($result[$i]);
            array_push($arrayRol, $datos);
        }

        return $arrayRol;
        
    }

    public function getAllObjsCount($filter = null) {
        
        $condition = $this->filterAllObj($filter);

        $query = " SELECT COUNT(*) AS total "
                . " FROM `allpack_consolidar_item` $condition " ;

        $result = $this->executeQuery($query);

        if ($result != null) {
            return $result[0]['total'];
        }

        return NULL;
    }

}

?>