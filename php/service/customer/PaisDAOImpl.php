<?php

class PaisDAOImpl extends mysql {

    private $_COLUMNS = " `allpack_pais`.`id_pais`,
    `allpack_pais`.`nombre_pais` ";

    private function createObj($value) {

        $obj = new Pais();
        $obj->setDes($value["nombre_pais"]);
        $obj->setId($value["id_pais"]);
        
        // $obj->setToken($value["token"]);
        
        return $obj;
    }

    /**
     * Save or update user on database 
     * @param Rol $obj
     * @return \Rol
     */
    public function saveObj($obj) {
        
        $id_pais = $obj->getId();
        $nombre_pais = $obj->getDes();
        
        if ($id_pais > 0) {

            //update object
            $query = "UPDATE `allpack_pais` "
                    . "SET `nombre_pais` = ?"
                    . "WHERE `id_pais` = ?";

            $stmt = $this->createPreparedStatement($query);

            mysqli_stmt_bind_param($stmt, "si", $nombre_pais, $id_pais);

            $this->executeUpdateOrDeletePreparedStatement($stmt, $query);
        } else {

            //new object
            $query = "INSERT INTO `allpack_pais` (`nombre_pais`) "
                    . "VALUES (?) ";
            
            $stmt = $this->createPreparedStatement($query);
            mysqli_stmt_bind_param($stmt, "s", $nombre_pais);

            $obj->setId($this->executeInsertWithAutoIncrementPreparedStatement($stmt, $query));
        }

        return $obj;
    }

    public function delObj(Rol $obj) {
        
        
        $id = $obj->getId();
        
        $query = "delete from `allpack_pais` where id_pais = ? ";
        $stmt = $this->createPreparedStatement($query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        $this->executeUpdateOrDeletePreparedStatement($stmt, $query);
        
        return 1;
    }
    
    public function filterAllObj($arrayfilter, $where = false) {

        $filter = "";

        if ($arrayfilter == null || !is_array($arrayfilter))
            return $filter;
        
        if (isset($arrayfilter["id"]) && $arrayfilter["id"] != null) {
            $filter .= ( $where ? " AND " : " WHERE " ) . " `allpack_pais`.id_pais in (" . $arrayfilter["id"] . ")";
            $where = true;
        }

        return $filter;
    }

    public function getAllObjs($filter = null) {
        
        $condition = $this->filterAllObj($filter, false);

        $query = " SELECT " . $this->_COLUMNS
                . " FROM `allpack_pais` "
                . $condition 
                . "ORDER BY `allpack_pais`.nombre_pais";

        // conditional to paginateion 
        if ($filter != null && isset($filter["show"]) && isset($filter["page"])) {
            $query .= " LIMIT " . $filter["page"] . "," . $filter["show"];
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
                . " FROM `allpack_pais` $condition "
                . "ORDER BY `allpack_pais`.nombre_pais";

        $result = $this->executeQuery($query);

        if ($result != null) {
            return $result[0]['total'];
        }

        return NULL;
    }

}

?>