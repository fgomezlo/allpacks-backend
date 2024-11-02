<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class CustomerControl {

    /** @var  CustomerDAOImpl */
    private $dao;
    private $daoPais;
    private $daoDestino;
    private $daoEstado;

    private function getDao() {
        if ($this->dao == null) {
            $this->dao = new CustomerDAOImpl();
        }

        return $this->dao;
    }
    
    private function getDaoPais() {
        if ($this->daoPais == null) {
            $this->daoPais = new PaisDAOImpl();
        }

        return $this->daoPais;
    }
    
    private function getDaoEstado() {
        if ($this->daoEstado == null) {
            $this->daoEstado = new EstadoDAOImpl();
        }

        return $this->daoEstado;
    }
    
    private function getDaoDestino() {
        if ($this->daoDestino == null) {
            $this->daoDestino = new DestinoDAOImpl();
        }

        return $this->daoDestino;
    }

    /**
     * 
     * @param type $id
     * @return Tag
     */
    public function getCustomerById($id) {

        $filter = ["id" => $id];
        $consulta = $this->getDao()->getAllObjs($filter);

        //TODO: aqui podemos agregar el extra
        return $consulta != null ? $consulta[0] : null;
    }

    public function saveCustomer($params, $userupdate) {

        $obj = new Customer();

        $update = false;
        if ($userupdate != null) {
            $obj = $userupdate;
            $update = true;
        } 

        $obj->setEstatus(isset($params["status"]) ? $GLOBALS["config"]["status"][$params["status"]]["db"] : $GLOBALS["config"]["status"]["disable"]["db"] );
        $obj->setDes(isset($params["name"]) ? $params["name"] : null);
        $obj->setDireccion(isset($params["direccion"]) ? $params["direccion"] : null);
        $obj->setCiudad(isset($params["ciudad"]) ? $params["ciudad"] : null);
        $obj->setEstado(isset($params["estado"]) ? $params["estado"] : null);
        $obj->setPais(isset($params["pais"]) ? $params["pais"] : null);
        $obj->setDestino(isset($params["destino"]) ? $params["destino"] : null);
        $obj->setTelefono(isset($params["telefono"]) ? $params["telefono"] : null);
        $obj->setEmail(isset($params["email"]) ? $params["email"] : null);
        $obj->setNotas(isset($params["notas"]) ? $params["notas"] : null);
        $obj->setCodigo(isset($params["codigo"]) ? $params["codigo"] : null);
        $obj->setEmpresa(isset($params["empresa"]) ? $params["empresa"] : null);
        $obj->setSync(isset($params["sync"]) ? $params["sync"] : null);
        
        if(isset($params["password"])) {
            $obj->setPassword($params["password"]);
        }
        
        //error_log(print_r($obj, true));
        $obj = $this->getDao()->saveObj($obj);

        if ($obj->getId() > 0) {

            /*$cMail = new MailControl();
            $cMail->sendActivationMessage($obj, $obj->getToken());
            */
            
            $obj->setEstatus($GLOBALS["config"]["status"]["active"]["swal"]);
            $obj->setMessage("El cliente '" . $obj->getDes() . "(" . $obj->getCodigo() . ")' fue "
                    . ($update ? "actualizado" : "agregado")
                    . " exitosamente");
        } else {
            // problemas de bd
            $obj->setEstatus($GLOBALS["config"]["status"]["error"]["swal"]);
            $obj->setMessage("Hubo un problema al actualizar o insertar el cliente en la BD");
        }

        return $obj;
    }

    /**
     * Get all users by filters
     * @param array $filter
     * @return array Customer
     */
    public function getALLCustomers($filter = null) {

        $consulta = $this->getDao()->getAllObjs($filter);

        if($consulta == null || count($consulta) == 0) 
            return $consulta;
        
        //$consulta = $this->addRolesToUserObj($consulta, $filter);
        
        return $consulta;
    }
    
    /*public function addRolesToUserObj($userarray, $filter) {
        
        if($userarray == null) {
            return $userarray;
        }
        
        if(isset($filter["getroles"]) && $filter["getroles"]) {
            
            for($i = 0; $i < count($userarray); $i++) {
                
                $usertmp = $userarray[$i];
                $filterRol = [
                    "rolbyuserid" => $usertmp->getId()
                ];
                
                $usertmp->setLstRoles(
                        $this->getDaoRol()->getAllObjs($filterRol)
                        );
                
                $userarray[$i] = $usertmp;
                
            }
            
        }
        
        return $userarray;
    }*/

    /**
     * All users with pagination
     * @param array $filter
     * @param int $show elements to show
     * @param int $page what page try to show
     * @return array ["total"=><int> , "data"=><array>objects]
     */
    public function getALLCustomersPagination($filter = null, $show = 50, $offset = 0) {

        $conditional = [];
        if ($filter != null) {
            $conditional = $filter;
        }
        $conditional["show"] = $show;
        $conditional["offset"] = $offset;

        $items = $this->getDao()->getAllObjs($conditional);
        //$data = $this->addRolesToUserObj($items, $filter);
        
        $consulta = [
            "total" => $this->getDao()->getAllObjsCount($conditional),
            "data" => $items
        ];

        return $consulta;
    }

    public function delCustomer($obj) {

        if ($obj != null) {

            if ($this->getDao()->delObj($obj) == 1) {
                $obj->setEstatus($GLOBALS["config"]["status"]["active"]["swal"]);
                $obj->setMessage("El cliente '" . $obj->getEmail() . "' ha sido eliminado exitosamente");
            } else {
                $obj->setEstatus($GLOBALS["config"]["status"]["error"]["swal"]);
                $obj->setMessage("Hubo un problema al eliminar el cliente de la base de datos");
            }
        } else {
            $obj = new User();
            $obj->setEstatus($GLOBALS["config"]["status"]["active"]["swal"]);
            $obj->setMessage("El cliente ha eliminar no se encuentra registrado");
        }

        return $obj;
    }
    
    public function getPaises() {
        return $this->getDaoPais()->getAllObjs();
    }
    
    public function getDestinos() {
        return $this->getDaoDestino()->getAllObjs();
    }
    
    public function getEstados() {
        return $this->getDaoEstado()->getAllObjs();
    }
}

?>