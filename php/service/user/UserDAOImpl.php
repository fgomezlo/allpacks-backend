<?php

class UserDAOImpl extends mysql {

    private $_COLUMNS = " allpack_usuario.id_usuario, "
            . "allpack_usuario.pass_usuario, allpack_usuario.log_usuario, "
            . "allpack_usuario.activo_usuario, allpack_usuario.mail_usuario, "
            . "allpack_usuario.nombre_usuario, allpack_usuario.token_reset ";

    private function createObj($value) {

        $obj = new User();
        $obj->setDes($value["nombre_usuario"]);
        $obj->setEmail($value["mail_usuario"]);
        $obj->setPassword($value["pass_usuario"]);
        $obj->setEstatus($value["activo_usuario"]);
        $obj->setId($value["id_usuario"]);
        $obj->setDni($value["log_usuario"]);
        $obj->setTokenReset($value["token_reset"]);
        
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
        $token_reset = $obj->getTokenReset();
        
        if ($id_usuario > 0) {

            //update object
            $query = "UPDATE `allpack_usuario` "
                    . "SET `nombre_usuario` =  ?, "
                    . "`log_usuario` =  ?, `pass_usuario` = ?, `mail_usuario` =  ?, "
                    . "`activo_usuario` =  ?, token_reset = ? "
                    . "WHERE id_usuario = ?";

            $stmt = $this->createPreparedStatement($query);

            mysqli_stmt_bind_param($stmt, "ssssisi", $nombre_usuario, $log_usuario, 
                    $pass_usuario, $mail_usuario, $activo_usuario, $token_reset, $id_usuario);

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
        try {
            $query = "delete from `allpack_rol_has_usuario` where id_usuario = ? ";
            $stmt = $this->createPreparedStatement($query);
            mysqli_stmt_bind_param($stmt, "i", $id);
            $this->executeUpdateOrDeletePreparedStatement($stmt, $query);

            $query = "delete from `allpack_usuario` where id_usuario = ? ";
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
        
        if (isset($arrayfilter["loguser"]) && $arrayfilter["loguser"] != null) {
            $filter .= ( $where ? " AND " : " WHERE " ) . " `allpack_usuario`.log_usuario like '" . $arrayfilter["loguser"] . "'";
            $where = true;
        }

        if (isset($arrayfilter["like"]) && $arrayfilter["like"] != null) {
            $filter .= ( $where ? " AND " : " WHERE " ) . " `allpack_usuario`.nombre_usuario like '%" . $arrayfilter["like"] . "%'";
            $where = true;
        }
        
        if (isset($arrayfilter["tokenreset"]) && $arrayfilter["tokenreset"] != null) {
            $filter .= ( $where ? " AND " : " WHERE " ) . " `allpack_usuario`.token_reset like '" . $arrayfilter["tokenreset"] . "'";
            $where = true;
        }
        
        if (isset($arrayfilter["filtervalue"]) && $arrayfilter["filtervalue"] != null) {
            $filter .= ( $where ? " AND " : " WHERE " ) . " ( " . 
                    "`allpack_usuario`.nombre_usuario like '%" . $arrayfilter["filtervalue"] . "%' ";
            $filter .= " OR `allpack_usuario`.log_usuario like '%" . $arrayfilter["filtervalue"] . "%' ";
            $filter .= " OR `allpack_usuario`.mail_usuario like '%" . $arrayfilter["filtervalue"] . "%' ";
            $filter .= " ) ";
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
                . " FROM `allpack_usuario` $condition "
                . "ORDER BY `allpack_usuario`.nombre_usuario";

        $result = $this->executeQuery($query);

        if ($result != null) {
            return $result[0]['total'];
        }

        return NULL;
    }
    
    public function addRolToUser($userid, Rol $rol) {
        
        $idRol = $rol->getId();
        $idUser = $userid;
        
        $query = "insert into `allpack_rol_has_usuario` (id_rol, id_usuario) values (? , ?) ";
        $stmt = $this->createPreparedStatement($query);
        mysqli_stmt_bind_param($stmt, "ii", $idRol, $idUser);
        $this->executeUpdateOrDeletePreparedStatement($stmt, $query);
        
        return 1;
        
    }
    
    public function deleteRolToUser($userid, Rol $rol) {
        
        $idRol = $rol->getId();
        $idUser = $userid;
        
        $query = "delete from `allpack_rol_has_usuario` where id_rol = ? and id_usuario = ? ";
        $stmt = $this->createPreparedStatement($query);
        mysqli_stmt_bind_param($stmt, "ii", $idRol, $idUser);
        $this->executeUpdateOrDeletePreparedStatement($stmt, $query);
        
        return 1;
        
    }

}

?>