<?php

class UserDAOImpl extends mysql {

    private $_COLUMNS = " allpack_usuario.id_usuario, "
            . "allpack_usuario.pass_usuario, allpack_usuario.log_usuario, "
            . "allpack_usuario.activo_usuario, allpack_usuario.mail_usuario, "
            . "allpack_usuario.nombre_usuario ";

    private function createObj($value) {

        $obj = new User();
        $obj->setDes($value["nombre_usuario"]);
        $obj->setEmail($value["mail_usuario"]);
        $obj->setPassword($value["pass_usuario"]);
        $obj->setEstatus($value["activo_usuario"]);
        $obj->setId($value["id_usuario"]);
        $obj->setDni($value["log_usuario"]);
        // $obj->setToken($value["token"]);
        
        return $obj;
    }

    /**
     * Save or update user on database 
     * @param User $obj
     * @return \User
     */
    public function saveObj($obj) {
        
        $id_usuario = $obj->getId();
        $nombre_usuario = $obj->getDes();
        $log_usuario = $obj->getDni();
        $pass_usuario = $obj->getPassword();
        $mail_usuario = $obj->getEmail();
        $activo_usuario = $obj->getEstatus();
        
        if ($id_usuario > 0) {

            //update object
            $query = "UPDATE `allpack_usuario` "
                    . "SET `nombre_usuario` =  ?, "
                    . "`log_usuario` =  ?, `pass_usuario` = ?, `mail_usuario` =  ?, "
                    . "`activo_usuario` =  ? "
                    . "WHERE id_usuario = ?";

            $stmt = $this->createPreparedStatement($query);

            mysqli_stmt_bind_param($stmt, "ssssii", $nombre_usuario, $log_usuario, 
                    $pass_usuario, $mail_usuario, $activo_usuario, $id_usuario);

            $this->executeUpdateOrDeletePreparedStatement($stmt, $query);
        } else {

            //new object
            $query = "INSERT INTO `allpack_usuario` (`nombre_usuario`, "
                    . "`log_usuario`, `pass_usuario`, `mail_usuario`, "
                    . "`activo_usuario`) VALUES (?,?,?,?,?) ";
            
            $stmt = $this->createPreparedStatement($query);
            mysqli_stmt_bind_param($stmt, "ssssi", $nombre_usuario, $log_usuario, 
                    $pass_usuario, $mail_usuario, $activo_usuario);

            $obj->setId($this->executeInsertWithAutoIncrementPreparedStatement($stmt, $query));
        }

        return $obj;
    }

    public function delObj(User $obj) {
        
        
        $id = $obj->getId();
        
        $query = "delete from `allpack_usuario` where id_usuario = ? ";
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
            $filter .= ( $where ? " AND " : " WHERE " ) . " `allpack_usuario`.activo_usuario = '" . $arrayfilter["status"] . "'";
            $where = true;
        }
        
        if (isset($arrayfilter["id"]) && $arrayfilter["id"] != null) {
            $filter .= ( $where ? " AND " : " WHERE " ) . " `allpack_usuario`.id_usuario in (" . $arrayfilter["id"] . ")";
            $where = true;
        }
        
        if (isset($arrayfilter["name"]) && $arrayfilter["name"] != null) {
            $filter .= ( $where ? " AND " : " WHERE " ) . " `allpack_usuario`.nombre_usuario like '" . $arrayfilter["name"] . "'";
            $where = true;
        }
        
        if (isset($arrayfilter["email"]) && $arrayfilter["email"] != null) {
            $filter .= ( $where ? " AND " : " WHERE " ) . " `allpack_usuario`.mail_usuario like '" . $arrayfilter["email"] . "'";
            $where = true;
        }

        if (isset($arrayfilter["like"]) && $arrayfilter["like"] != null) {
            $filter .= ( $where ? " AND " : " WHERE " ) . " `allpack_usuario`.nombre_usuario like '%" . $arrayfilter["like"] . "%'";
            $where = true;
        }

        return $filter;
    }

    public function getAllObjs($filter = null) {
        
        $condition = $this->filterAllObj($filter, false);

        $query = " SELECT " . $this->_COLUMNS
                . " FROM `allpack_usuario` "
                . $condition 
                . "ORDER BY `allpack_usuario`.nombre_usuario";

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
                . " FROM `allpack_usuario` $condition "
                . "ORDER BY `allpack_usuario`.nombre_usuario";

        $result = $this->executeQuery($query);

        if ($result != null) {
            return $result[0]['total'];
        }

        return NULL;
    }

}

?>