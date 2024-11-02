<?php

class CustomerDAOImpl extends mysql {

    private $_COLUMNS = " `allpack_cliente`.`id_cliente`, `allpack_cliente`.`codigo_cliente`, "
            . "`allpack_cliente`.`empresa_cliente`, `allpack_cliente`.`nombre_cliente`, "
            . "`allpack_cliente`.`direccion_cliente`, `allpack_cliente`.`ciudad_cliente`, "
            . "`allpack_cliente`.`estado_cliente`, `allpack_cliente`.`pais_cliente`, "
            . "`allpack_cliente`.`destino_cliente`, `allpack_cliente`.`telefono_cliente`, "
            . "`allpack_cliente`.`email_cliente`, `allpack_cliente`.`notas`, "
            . "`allpack_cliente`.`activo_cliente`, `allpack_cliente`.`password_cliente`, "
            . "`allpack_cliente`.`fecha_cliente`, `allpack_cliente`.`sync` ";

    private function createObj($value) {

        $obj = new Customer();
        $obj->setCiudad($value["ciudad_cliente"]);
        $obj->setCodigo($value["codigo_cliente"]);
        $obj->setDateCreated($value["fecha_cliente"]);
        $obj->setDes($value["nombre_cliente"]);
        $obj->setDestino($value["destino_cliente"]);
        $obj->setDireccion($value["direccion_cliente"]);
        $obj->setEmail($value["email_cliente"]);
        $obj->setEmpresa($value["empresa_cliente"]);
        $obj->setEstado($value["estado_cliente"]);
        $obj->setEstatus($value["activo_cliente"]);
        $obj->setId($value["id_cliente"]);
        $obj->setNotas($value["notas"]);
        $obj->setPais($value["pais_cliente"]);
        $obj->setPassword($value["password_cliente"]);
        $obj->setSync($value["sync"]);
        $obj->setTelefono($value["telefono_cliente"]);
        
        // $obj->setToken($value["token"]);
        
        return $obj;
    }

    /**
     * Save or update user on database 
     * @param Customer $obj
     * @return \Customer
     */
    public function saveObj($obj) {
        
        $id_cliente = $obj->getId();
        $codigo_cliente = $obj->getCodigo();
        $empresa_cliente = $obj->getEmpresa();
        $nombre_cliente = $obj->getDes();
        $direccion_cliente = $obj->getDireccion();
        $ciudad_cliente = $obj->getCiudad();
        $estado_cliente = $obj->getEstado();
        $pais_cliente = $obj->getPais(); // id int
        $destino_cliente = $obj->getDestino(); // id int
        $telefono_cliente = $obj->getTelefono();
        $email_cliente = $obj->getEmail();
        $notas = $obj->getNotas();
        $activo_cliente = $obj->getEstatus(); // activo = 1 , disabled= 0
        $password_cliente = $obj->getPassword();
        $sync = $obj->getSync();
        
        if ($id_cliente > 0) {

            //update object
            $query = "UPDATE `allpack_cliente` "
                    . "SET codigo_cliente = ?, empresa_cliente =? , nombre_cliente = ?,"
                    . " direccion_cliente = ?, ciudad_cliente =? , estado_cliente = ?,"
                    . " pais_cliente = ?, destino_cliente =?, telefono_cliente = ?,"
                    . " email_cliente = ?, notas = ?, activo_cliente = ?, "
                    . "password_cliente = ?, sync = ? "
                    . "WHERE id_cliente = ?";

            $stmt = $this->createPreparedStatement($query);

            mysqli_stmt_bind_param($stmt, "ssssssiisssisii", $codigo_cliente,
                    $empresa_cliente, $nombre_cliente, $direccion_cliente, 
                    $ciudad_cliente, $estado_cliente, $pais_cliente, 
                    $destino_cliente, $telefono_cliente, $email_cliente, $notas, 
                    $activo_cliente, $password_cliente, $sync, $id_cliente);

            $this->executeUpdateOrDeletePreparedStatement($stmt, $query);
        } else {

            //new object
            $query = "INSERT INTO `allpack_cliente` (codigo_cliente, empresa_cliente, "
                    . "nombre_cliente, direccion_cliente, ciudad_cliente, "
                    . "estado_cliente, pais_cliente, destino_cliente, "
                    . "telefono_cliente, email_cliente, notas, "
                    . "activo_cliente, password_cliente, fecha_cliente, sync) "
                    . "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, now(), 0) ";
            
            $stmt = $this->createPreparedStatement($query);
            mysqli_stmt_bind_param($stmt, "ssssssiisssis", $codigo_cliente,
                    $empresa_cliente, $nombre_cliente, $direccion_cliente, 
                    $ciudad_cliente, $estado_cliente, $pais_cliente, 
                    $destino_cliente, $telefono_cliente, $email_cliente, $notas, 
                    $activo_cliente, $password_cliente);

            $obj->setId($this->executeInsertWithAutoIncrementPreparedStatement($stmt, $query));
        }

        return $obj;
    }

    public function delObj(Customer $obj) {
        
        
        $id = $obj->getId();
        try {
            $query = "delete from `allpack_cliente` where id_cliente = ? ";
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
            $filter .= ( $where ? " AND " : " WHERE " ) . " `allpack_cliente`.activo_cliente = '" . $arrayfilter["status"] . "'";
            $where = true;
        }
        
        if (isset($arrayfilter["id"]) && $arrayfilter["id"] != null) {
            $filter .= ( $where ? " AND " : " WHERE " ) . " `allpack_cliente`.id_cliente in (" . $arrayfilter["id"] . ")";
            $where = true;
        }
        
        if (isset($arrayfilter["name"]) && $arrayfilter["name"] != null) {
            $filter .= ( $where ? " AND " : " WHERE " ) . " `allpack_cliente`.nombre_cliente like '" . $arrayfilter["name"] . "'";
            $where = true;
        }
        
        if (isset($arrayfilter["codigo"]) && $arrayfilter["codigo"] != null) {
            $filter .= ( $where ? " AND " : " WHERE " ) . " `allpack_cliente`.codigo_cliente like '" . $arrayfilter["codigo"] . "'";
            $where = true;
        }
        
        if (isset($arrayfilter["email"]) && $arrayfilter["email"] != null) {
            $filter .= ( $where ? " AND " : " WHERE " ) . " `allpack_cliente`.email_cliente like '" . $arrayfilter["email"] . "'";
            $where = true;
        }

        if (isset($arrayfilter["like"]) && $arrayfilter["like"] != null) {
            $filter .= ( $where ? " AND " : " WHERE " ) . " `allpack_cliente`.nombre_cliente like '%" . $arrayfilter["like"] . "%'";
            $where = true;
        }
        
        if (isset($arrayfilter["filtervalue"]) && $arrayfilter["filtervalue"] != null) {
            $filter .= ( $where ? " AND " : " WHERE " ) . " ( " . 
                    "`allpack_cliente`.nombre_cliente like '%" . $arrayfilter["filtervalue"] . "%' ";
            $filter .= " OR `allpack_cliente`.codigo_cliente like '%" . $arrayfilter["filtervalue"] . "%' ";
            $filter .= " OR `allpack_cliente`.email_cliente like '%" . $arrayfilter["filtervalue"] . "%' ";
            $filter .= " ) ";
            $where = true;
        }

        return $filter;
    }

    public function getAllObjs($filter = null) {
        
        $condition = $this->filterAllObj($filter, false);

        $query = " SELECT " . $this->_COLUMNS
                . " FROM `allpack_cliente` "
                . $condition 
                . "ORDER BY `allpack_cliente`.nombre_cliente";

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
                . " FROM `allpack_cliente` $condition "
                . "ORDER BY `allpack_cliente`.nombre_cliente";

        $result = $this->executeQuery($query);

        if ($result != null) {
            return $result[0]['total'];
        }

        return NULL;
    }

}

?>