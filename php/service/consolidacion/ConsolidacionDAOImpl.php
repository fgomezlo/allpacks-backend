<?php

class ConsolidacionDAOImpl extends mysql {

    private $_COLUMNS = " `allpack_consolidar`.`id_consolidar`, "
            . "`allpack_consolidar`.`id_cliente`, `allpack_consolidar`.`nota_cliente`, "
            . "`allpack_consolidar`.`status_consolidar`, "
            . "date_format(`allpack_consolidar`.`fecha_actualizacion`,'%d/%m/%Y %H:%i') as fecha_actualizacion, "
            . "`allpack_consolidar`.`id_usuario`, `allpack_consolidar`.`observacion`, "
            . "`allpack_consolidar`.`tipo_servicio` ,"
            . "(select date_format(min(fecha_actualizacion), '%d/%m/%Y %H:%i') "
            . "from allpack_hist_consolidar "
            . "where allpack_hist_consolidar.id_consolidar =  allpack_consolidar.id_consolidar) as fecha_creacion";
    

    private function createObj($value) {

        $obj = new Consolidacion();
        $obj->setId($value["id_consolidar"]);
        $obj->setIdcliente($value["id_cliente"]);
        $obj->setNota($value["nota_cliente"]);
        $obj->setEstatus($value["status_consolidar"]);
        $obj->setDateUpdated($value["fecha_actualizacion"]);
        $obj->setIdusuario($value["id_usuario"]);
        $obj->setObservacion($value["observacion"]);
        $obj->setTipoServicio($value["tipo_servicio"]);
        $obj->setDateUpdated($value["fecha_actualizacion"]);
        $obj->setDateCreated($value["fecha_creacion"] != null ? $value["fecha_creacion"] : $value["fecha_actualizacion"]);
        
        return $obj;
    }

    /**
     * Save or update user on database 
     * @param Consolidacion $obj
     * @return \Consolidacion
     */
    public function saveObj($obj) {
        
         $id_consolidar = $obj->getId();
         $id_cliente = $obj->getIdcliente();
         $nota_cliente = $obj->getNota();
         $status_consolidar = $obj->getEstatus();
         $id_usuario = $obj->getIdusuario();
         $observacion = $obj->getObservacion();
         $tipo_servicio = $obj->getTipoServicio();
        
        if ($id_consolidar > 0) {
            
            // auditoria
            $this->saveAuditConsolidarObj($obj->getId());
            
            //update object
            $query = "UPDATE `allpack_consolidar` "
                    . "SET id_cliente = ? , nota_cliente = ?, status_consolidar = ?, "
                    . "fecha_actualizacion = now(), id_usuario = ?, "
                    . "observacion = ?, tipo_servicio = ? "
                    . "WHERE id_consolidar = ?";

            $stmt = $this->createPreparedStatement($query);

            mysqli_stmt_bind_param($stmt, "isiisii", $id_cliente, $nota_cliente, 
                    $status_consolidar, $id_usuario, $observacion, 
                    $tipo_servicio, $id_consolidar);

            $this->executeUpdateOrDeletePreparedStatement($stmt, $query);
        } else {

            //new object
            $query = "INSERT INTO `allpack_consolidar` ("
                    . "id_cliente, nota_cliente, status_consolidar, "
                    . "fecha_actualizacion, id_usuario, observacion, tipo_servicio) "
                    . "VALUES (?, ?, ?, now(), ?, ?, ?) ";
            
            $stmt = $this->createPreparedStatement($query);
            mysqli_stmt_bind_param($stmt, "isiisi", $id_cliente, $nota_cliente, 
                    $status_consolidar, $id_usuario, $observacion, 
                    $tipo_servicio);

            $obj->setId($this->executeInsertWithAutoIncrementPreparedStatement($stmt, $query));
        }

        return $obj;
    }

    public function delObj(Consolidacion $obj) {
        
        
        $id = $obj->getId();
        try {
            $query = "delete from `allpack_hist_consolidar_item` "
                    . "where exists (select * "
                    . "from allpack_hist_consolidar "
                    . "where allpack_hist_consolidar.id_consolidar = ? and "
                    . "allpack_hist_consolidar_item.id_hist_consolidar = "
                    . " allpack_hist_consolidar_item.id_hist_consolidar) ";
            $stmt = $this->createPreparedStatement($query);
            mysqli_stmt_bind_param($stmt, "i", $id);
            $this->executeUpdateOrDeletePreparedStatement($stmt, $query);
            
            $query = "delete from `allpack_hist_consolidar` where id_consolidar = ? ";
            $stmt = $this->createPreparedStatement($query);
            mysqli_stmt_bind_param($stmt, "i", $id);
            $this->executeUpdateOrDeletePreparedStatement($stmt, $query);
            
            $query = "delete from `allpack_consolidar_item` where id_consolidar = ? ";
            $stmt = $this->createPreparedStatement($query);
            mysqli_stmt_bind_param($stmt, "i", $id);
            $this->executeUpdateOrDeletePreparedStatement($stmt, $query);
            
            $query = "delete from `allpack_consolidar` where id_consolidar = ? ";
            $stmt = $this->createPreparedStatement($query);
            mysqli_stmt_bind_param($stmt, "i", $id);
            $this->executeUpdateOrDeletePreparedStatement($stmt, $query);
        } catch (Exception $e) {
            //error_log(print_r($e, true));
            return false;
        }
        
        return true;
    }
    
    public function filterAllObj($arrayfilter, $where = false) {

        $filter = "";

        if ($arrayfilter == null || !is_array($arrayfilter))
            return $filter;

        if (isset($arrayfilter["status"]) && $arrayfilter["status"] != null) {
            $filter .= ( $where ? " AND " : " WHERE " ) . " `allpack_consolidar`.status_consolidar in (" . $arrayfilter["status"] . ")";
            $where = true;
        }
        
        if (isset($arrayfilter["id"]) && $arrayfilter["id"] != null) {
            $filter .= ( $where ? " AND " : " WHERE " ) . " `allpack_consolidar`.id_consolidar in (" . $arrayfilter["id"] . ")";
            $where = true;
        }
        
        if (isset($arrayfilter["tiposervicio"]) && $arrayfilter["tiposervicio"] != null) {
            $filter .= ( $where ? " AND " : " WHERE " ) . " `allpack_consolidar`.tipo_servicio in (" . $arrayfilter["tiposervicio"] . ")";
            $where = true;
        }
        
        if(isset($arrayfilter["fromdate"]) || isset($arrayfilter["todate"]) ) {
            
            if(isset($arrayfilter["fromdate"]) && isset($arrayfilter["todate"])) {
                // between
                $filter .= ( $where ? " AND " : " WHERE " ) . 
                        " `allpack_consolidar`.fecha_actualizacion BETWEEN "
                        . " str_to_date('" . $arrayfilter["fromdate"] . "','%Y-%m-%d') AND "
                        . " str_to_date('" . $arrayfilter["todate"] . "','%Y-%m-%d') ";

            } else if (isset($arrayfilter["fromdate"])) {
                // >= only
                $filter .= ( $where ? " AND " : " WHERE " ) . 
                        " `allpack_consolidar`.fecha_actualizacion >= "
                        . " str_to_date('" . $arrayfilter["fromdate"] . "','%Y-%m-%d')";
            } else {
                // <= only
                $filter .= ( $where ? " AND " : " WHERE " ) . 
                        " `allpack_consolidar`.fecha_actualizacion <= "
                        . " str_to_date('" . $arrayfilter["todate"] . "','%Y-%m-%d')";
            }
            
            $where = true;
            
        }
        
        if (isset($arrayfilter["filtervalue"]) && $arrayfilter["filtervalue"] != null) {
            $filter .= ( $where ? " AND " : " WHERE " ) . " ( " 
                    . "EXISTS (select * from `allpack_cliente` "
                    . "WHERE `allpack_cliente`.codigo_cliente like '" . $arrayfilter["filtervalue"] . "' "
                    . "AND `allpack_cliente`.id_cliente = allpack_consolidar.id_cliente ) ";
            $filter .= " OR `allpack_consolidar`.id_consolidar = " . $arrayfilter["filtervalue"] . " ";
            $filter .= " ) ";
            $where = true;
        }
        
        if (isset($arrayfilter["tracking"]) && $arrayfilter["tracking"] != null) {
            $filter .= ( $where ? " AND " : " WHERE " ) . " ( " 
                    . "EXISTS (select * from `allpack_cliente` "
                    . "WHERE `allpack_cliente`.codigo_cliente like '" . $arrayfilter["tracking"] . "' "
                    . "AND `allpack_cliente`.id_cliente = allpack_consolidar.id_cliente ) ";
            $filter .= " OR "
                    . "EXISTS (select * "
                    . "from `allpack_consolidar_item` aci "
                    . "where aci.tracking_consolidar like '" . $arrayfilter["tracking"] . "' and "
                    . "`allpack_consolidar`.id_consolidar = aci.id_consolidar ) ";
            $filter .= " ) ";
            $where = true;
        }

        return $filter;
    }

    public function getAllObjs($filter = null) {
        
        $condition = $this->filterAllObj($filter, false);

        $query = " SELECT " . $this->_COLUMNS
                . " FROM `allpack_consolidar` "
                . $condition 
                . "ORDER BY `allpack_consolidar`.id_consolidar desc";

        // conditional to paginateion 
        if ($filter != null && isset($filter["show"]) && isset($filter["offset"])) {
            $query .= " LIMIT " . $filter["offset"] . "," . $filter["show"];
        }

        //check if filter contains limit of products to show
        if ($filter != null && isset($filter["limit"])) {
            $query .= " LIMIT " . $filter["limit"] . " ";
        }
        
        error_log($query);
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
                . " FROM `allpack_consolidar` $condition ";

        $result = $this->executeQuery($query);

        if ($result != null) {
            return $result[0]['total'];
        }

        return NULL;
    }
    
    /**
     * funcion que guarda el historial referente a la informacion de una solicitud de reempaque
     * @param int $idConsolidar
     */
    private function saveAuditConsolidarObj($idConsolidar) {

        $queryh = "insert into allpack_hist_consolidar (fecha_actualizacion,
                    id_cliente,
                    id_consolidar,
                    id_usuario,
                    nota_cliente,
                    observacion,
                    status_consolidar,
                    tipo_servicio) "
                . "(select fecha_actualizacion,
                    id_cliente,
                    id_consolidar,
                    id_usuario,
                    nota_cliente,
                    observacion,
                    status_consolidar,
                    tipo_servicio "
                . "from allpack_consolidar "
                . "where id_consolidar = " . $idConsolidar . ")";

        $tmp = $this->executeInsertWithAutoIncrement($queryh);

        $queryh = " insert into allpack_hist_consolidar_item ( 
            id_hist_consolidar, 
            carrier_consolidar,
            des_consolidar,
            status_consolidar_item,
            tracking_consolidar,warehouse, nota, valor_consolidar) (select $tmp, 
                carrier_consolidar,
                des_consolidar,
                status_consolidar_item,
                tracking_consolidar ,warehouse, nota, valor_consolidar
                from allpack_consolidar_item 
                where id_consolidar = " . $idConsolidar . ")";
       // error_log($queryh);
        $this->executeUpdateOrDelete($queryh);

        return true;
    }

}

?>