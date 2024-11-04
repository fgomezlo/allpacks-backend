<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class ConsolidacionControl {

    /** @var  ConsolidacionDAOImpl */
    private $dao;
    private $daoItem;
    private $daoEstado;

    private function getDao() {
        if ($this->dao == null) {
            $this->dao = new ConsolidacionDAOImpl();
        }

        return $this->dao;
    }
    
    private function getDaoItem() {
        if ($this->daoItem == null) {
            $this->daoItem = new ConsolidacionItemDAOImpl();
        }

        return $this->daoItem;
    }
    
    private function getDaoEstado() {
        if ($this->daoEstado == null) {
            $this->daoEstado = new EstadoDAOImpl();
        }

        return $this->daoEstado;
    }

    public function saveConsolidacion($params, $objupdate) {

        $obj = new Consolidacion();

        $update = false;
        if ($objupdate != null) {
            $obj = $objupdate;
            $update = true;
        } 

        $obj->setEstatus(isset($params["status"]) ? $params["status"] : 1 );
        $obj->setNota(isset($params["nota"]) ? $params["nota"] : null);
        $obj->setObservacion(isset($params["observacion"]) ? $params["observacion"] : null);
        $obj->setTipoServicio(isset($params["tiposervicio"]) ? $params["tiposervicio"] : null);
        $obj->setIdcliente(isset($params["cliente"]) && isset($params["cliente"]["id"]) ? $params["cliente"]["id"] : null);
        $obj->setIdusuario(isset($params["usuario"]) && isset($params["usuario"]["id"]) ? $params["usuario"]["id"] : null);
            
        //error_log(print_r($obj, true));
        $obj = $this->getDao()->saveObj($obj);

        if ($obj->getId() > 0) {

            /*$cMail = new MailControl();
            $cMail->sendActivationMessage($obj, $obj->getToken());
            */
            
            $obj->setEstatus($GLOBALS["config"]["status"]["active"]["swal"]);
            $obj->setMessage("Consolidacion '" . $obj->getDes() . "(" . $obj->getId() . ")' fue "
                    . ($update ? "actualizada" : "agregada")
                    . " exitosamente");
        } else {
            // problemas de bd
            $obj->setEstatus($GLOBALS["config"]["status"]["error"]["swal"]);
            $obj->setMessage("Hubo un problema al actualizar o insertar la consolidacion en la BD");
        }

        return $obj;
    }

    /**
     * Get all users by filters
     * @param array $filter
     * @return array Customer
     */
    public function getALLConsolidacion($filter = null) {

        $consulta = $this->getDao()->getAllObjs($filter);

        if($consulta == null || count($consulta) == 0) 
            return $consulta;
        
        $consulta = $this->addItemsToConsolidacionObj($consulta, $filter);
        
        return $consulta;
    }
    
    public function addItemsToConsolidacionObj($consolidarray, $filter) {
        
        if($consolidarray == null) {
            return $consolidarray;
        }
        
        if(isset($filter["getitems"]) && $filter["getitems"]) {
            
            for($i = 0; $i < count($consolidarray); $i++) {
                
                //$consolidaciontmp = new Consolidacion();
                $consolidaciontmp = $consolidarray[$i];
                $filterItemConsolidacion = [
                    "consolidacionid" => $consolidaciontmp->getId()
                ];
                
                $consolidaciontmp->setLstItems(
                        $this->getDaoItem()->getAllObjs($filterItemConsolidacion)
                        );
                
                $consolidarray[$i] = $consolidaciontmp;
                
            }
            
        }
        
        return $consolidarray;
    }

    /**
     * All users with pagination
     * @param array $filter
     * @param int $show elements to show
     * @param int $page what page try to show
     * @return array ["total"=><int> , "data"=><array>objects]
     */
    public function getALLConsolidacionPagination($filter = null, $show = 50, $offset = 0) {

        $conditional = [];
        if ($filter != null) {
            $conditional = $filter;
        }
        $conditional["show"] = $show;
        $conditional["offset"] = $offset;

        $items = $this->getDao()->getAllObjs($conditional);
        $data = $this->addItemsToConsolidacionObj($items, $filter);
        
        $consulta = [
            "total" => $this->getDao()->getAllObjsCount($conditional),
            "data" => $data
        ];

        return $consulta;
    }

    public function delConsolidacion($obj) {

        if ($obj != null) {

            if ($this->getDao()->delObj($obj) == 1) {
                $obj->setEstatus($GLOBALS["config"]["status"]["active"]["swal"]);
                $obj->setMessage("La consolidacion '" . $obj->getId() . "' ha sido eliminada exitosamente");
            } else {
                $obj->setEstatus($GLOBALS["config"]["status"]["error"]["swal"]);
                $obj->setMessage("Hubo un problema al eliminar la consolidacion de la base de datos");
            }
        } else {
            $obj = new Combo();
            $obj->setEstatus($GLOBALS["config"]["status"]["active"]["swal"]);
            $obj->setMessage("La consolidacion ha eliminar no se encuentra registrada");
        }

        return $obj;
    }
    
    public function getEstados() {
        return $this->getDaoEstado()->getAllObjs();
    }
}

?>