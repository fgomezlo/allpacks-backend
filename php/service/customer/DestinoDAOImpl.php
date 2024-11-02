<?php

class DestinoDAOImpl extends mysql {

    private $_COLUMNS = " `allpack_destino`.`id_destino`,
    `allpack_destino`.`nombre_destino` ";

    private function createObj($value) {

        $obj = new Destino();
        $obj->setDes($value["nombre_destino"]);
        $obj->setId($value["id_destino"]);
        
        // $obj->setToken($value["token"]);
        
        return $obj;
    }

    /**
     * Save or update user on database 
     * @param Rol $obj
     * @return \Rol
     */
    public function saveObj($obj) {
        
        $id_destino = $obj->getId();
        $nombre_destino = $obj->getDes();
        
        if ($id_destino > 0) {

            //update object
            $query = "UPDATE `allpack_destino` "
                    . "SET `nombre_destino` = ?"
                    . "WHERE `id_destino` = ?";

            $stmt = $this->createPreparedStatement($query);

            mysqli_stmt_bind_param($stmt, "si", $nombre_destino, $id_destino);

            $this->executeUpdateOrDeletePreparedStatement($stmt, $query);
        } else {

            //new object
            $query = "INSERT INTO `allpack_destino` (`nombre_destino`) "
                    . "VALUES (?) ";
            
            $stmt = $this->createPreparedStatement($query);
            mysqli_stmt_bind_param($stmt, "s", $nombre_destino);

            $obj->setId($this->executeInsertWithAutoIncrementPreparedStatement($stmt, $query));
        }

        return $obj;
    }

    public function delObj(Rol $obj) {
        
        
        $id = $obj->getId();
        
        $query = "delete from `allpack_destino` where id_destino = ? ";
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
            $filter .= ( $where ? " AND " : " WHERE " ) . " `allpack_destino`.id_destino in (" . $arrayfilter["id"] . ")";
            $where = true;
        }

        return $filter;
    }

    public function getAllObjs($filter = null) {
        
        $condition = $this->filterAllObj($filter, false);

        $query = " SELECT " . $this->_COLUMNS
                . " FROM `allpack_destino` "
                . $condition 
                . "ORDER BY `allpack_destino`.nombre_destino";

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
                . " FROM `allpack_destino` $condition "
                . "ORDER BY `allpack_destino`.nombre_destino";

        $result = $this->executeQuery($query);

        if ($result != null) {
            return $result[0]['total'];
        }

        return NULL;
    }

}

?>