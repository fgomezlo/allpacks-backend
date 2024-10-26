<?php

class MenuDAOImpl extends mysql {

    private $_COLUMNS = "`allpack_menu`.`id_menu`, `allpack_menu`.`des_menu`, "
            . "`allpack_menu`.`visible_menu`, `allpack_menu`.`url_menu`, "
            . "`allpack_menu`.`activo_menu`, `allpack_menu`.`order_menu`, "
            . "`allpack_menu`.`padre_id_menu`, `allpack_menu`.`css_menu`, "
            . "`allpack_menu`.`javascript_menu` ";

    private function createObj($value) {

        $obj = new Menu();
        $obj->setDes($value["des_menu"]);
        $obj->setId($value["id_menu"]);
        $obj->setEstatus($value["activo_menu"]);
        $obj->setUrl($value["url_menu"]);
        $obj->setVisible($value["visible_menu"]);
        $obj->setOrder($value["order_menu"]);
        $obj->setPadre($value["padre_id_menu"]);
        $obj->setCssChild($value["css_menu"]);
        $obj->setJavascript($value["javascript_menu"]);
        
        // $obj->setToken($value["token"]);
        
        return $obj;
    }

    /**
     * Save or update user on database 
     * @param Menu $obj
     * @return \Menu
     */
    public function saveObj($obj) {
        
        $id_menu = $obj->getId();
        $des_menu = $obj->getDes();
        $visible_menu = $obj->getVisible();
        $url_menu = $obj->getUrl();
        $activo_menu = $obj->getEstatus();
        $order_menu = $obj->getOrder();
        $padre_id_menu = $obj->getPadre();
        $css_menu = $obj->getCssChild();
        $javascript_menu = $obj->getJavascript();
        
        if ($id_menu > 0) {

            //update object
            $query = "UPDATE `allpack_menu` "
                    . "SET `des_menu` = ? , "
                    . "`visible_menu` = ?, `url_menu` = ?, `activo_menu` = ?, "
                    . "`order_menu` = ?, `padre_id_menu` = ?, `css_menu` = ?, "
                    . "`javascript_menu` = ? "
                    . "WHERE `id_menu` = ?";

            $stmt = $this->createPreparedStatement($query);

            mysqli_stmt_bind_param($stmt, "sisiiissi", $des_menu, $visible_menu, 
                    $url_menu, $activo_menu, $order_menu, $padre_id_menu, 
                    $css_menu, $javascript_menu, $id_menu);

            $this->executeUpdateOrDeletePreparedStatement($stmt, $query);
        } else {

            //new object
            $query = "INSERT INTO `allpack_menu` (`des_menu`, "
                    . "`visible_menu`, `url_menu`, `activo_menu`, "
                    . "`order_menu`, `padre_id_menu`, `css_menu`, "
                    . "`javascript_menu`) VALUES (?, ?, ?, ?, ?, ?, ?, ?) ";
            
            $stmt = $this->createPreparedStatement($query);
            mysqli_stmt_bind_param($stmt, "sisiiiss", $des_menu, $visible_menu, 
                    $url_menu, $activo_menu, $order_menu, $padre_id_menu, 
                    $css_menu, $javascript_menu);

            $obj->setId($this->executeInsertWithAutoIncrementPreparedStatement($stmt, $query));
        }

        return $obj;
    }

    public function delObj(Menu $obj) {
        
        
        $id = $obj->getId();
        
        $query = "delete from `allpack_menu` where id_menu = ? ";
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
            $filter .= ( $where ? " AND " : " WHERE " ) . " `allpack_menu`.activo_menu = '" . $arrayfilter["status"] . "'";
            $where = true;
        }
        
        if (isset($arrayfilter["id"]) && $arrayfilter["id"] != null) {
            $filter .= ( $where ? " AND " : " WHERE " ) . " `allpack_menu`.id_menu in (" . $arrayfilter["id"] . ")";
            $where = true;
        }

        return $filter;
    }

    public function getAllObjs($filter = null) {
        
        $condition = $this->filterAllObj($filter, false);

        $query = " SELECT " . $this->_COLUMNS
                . " FROM `allpack_menu` "
                . $condition 
                . "ORDER BY `allpack_menu`.des_menu";

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
                . " FROM `allpack_menu` $condition "
                . "ORDER BY `allpack_menu`.des_menu";

        $result = $this->executeQuery($query);

        if ($result != null) {
            return $result[0]['total'];
        }

        return NULL;
    }

}

?>