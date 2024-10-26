<?php

class RolDAOImpl extends mysql {

    private $_COLUMNS = " `allpack_rol`.`id_rol`, `allpack_rol`.`des_rol`, "
            . "`allpack_rol`.`activo_rol` ";

    private function createObj($value) {

        $obj = new Rol();
        $obj->setDes($value["des_rol"]);
        $obj->setEstatus($value["activo_rol"]);
        $obj->setId($value["id_rol"]);
        
        // $obj->setToken($value["token"]);
        
        return $obj;
    }

    /**
     * Save or update user on database 
     * @param Rol $obj
     * @return \Rol
     */
    public function saveObj($obj) {
        
        $id_rol = $obj->getId();
        $des_rol = $obj->getDes();
        $activo_rol = $obj->getEstatus();
        
        if ($id_rol > 0) {

            //update object
            $query = "UPDATE `allpack_rol` "
                    . "SET `des_rol` = ?, `activo_rol` =?  "
                    . "WHERE `id_rol` = ?";

            $stmt = $this->createPreparedStatement($query);

            mysqli_stmt_bind_param($stmt, "sii", $des_rol, $activo_rol, $id_rol);

            $this->executeUpdateOrDeletePreparedStatement($stmt, $query);
        } else {

            //new object
            $query = "INSERT INTO `allpack_rol` (`des_rol`, "
                    . "`activo_rol`) VALUES (?,?) ";
            
            $stmt = $this->createPreparedStatement($query);
            mysqli_stmt_bind_param($stmt, "si", $des_rol, $activo_rol);

            $obj->setId($this->executeInsertWithAutoIncrementPreparedStatement($stmt, $query));
        }

        return $obj;
    }

    public function delObj(Rol $obj) {
        
        
        $id = $obj->getId();
        
        $query = "delete from `allpack_rol` where id_rol = ? ";
        $stmt = $this->createPreparedStatement($query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        $this->executeUpdateOrDeletePreparedStatement($stmt, $query);
        
        return 1;
    }
    
    public function filterAllObj($arrayfilter, $where = false) {

        $filter = "";

        if ($arrayfilter == null || !is_array($arrayfilter))
            return $filter;

        if (isset($arrayfilter["status"]) && $arrayfilter["status"] != null) {
            $filter .= ( $where ? " AND " : " WHERE " ) . " `allpack_rol`.activo_rol = '" . $arrayfilter["status"] . "'";
            $where = true;
        }
        
        if (isset($arrayfilter["id"]) && $arrayfilter["id"] != null) {
            $filter .= ( $where ? " AND " : " WHERE " ) . " `allpack_rol`.id_rol in (" . $arrayfilter["id"] . ")";
            $where = true;
        }
        
        if (isset($arrayfilter["rolbyuserid"]) && $arrayfilter["rolbyuserid"] != null) {
            $filter .= ( $where ? " AND " : " WHERE " ) . " exists (" 
                    . "SELECT * "
                    . "FROM allpack_rol_has_usuario rol_has_usuario "
                    . "WHERE rol_has_usuario.id_rol = `allpack_rol`.id_rol "
                    . "AND rol_has_usuario.id_usuario = " . $arrayfilter["rolbyuserid"] 
                    . ")";
            $where = true;
        }

        return $filter;
    }

    public function getAllObjs($filter = null) {
        
        $condition = $this->filterAllObj($filter, false);

        $query = " SELECT " . $this->_COLUMNS
                . " FROM `allpack_rol` "
                . $condition 
                . "ORDER BY `allpack_rol`.des_rol";

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
                . " FROM `allpack_rol` $condition "
                . "ORDER BY `allpack_rol`.des_rol";

        $result = $this->executeQuery($query);

        if ($result != null) {
            return $result[0]['total'];
        }

        return NULL;
    }

}

?>