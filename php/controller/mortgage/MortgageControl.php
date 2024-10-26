<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class MortgageControl {

    /** @var  MortgageDAOImpl */
    private $dao;

    private function getDao() {
        if ($this->dao == null) {
            $this->dao = new MortgageDAOImpl();
        }

        return $this->dao;
    }

    /**
     * 
     * @param type $id
     * @return Tag
     */
    public function getMortgageById($id) {

        $filter = ["id" => $id];
        $consulta = $this->getDao()->getAllMortgageObjs($filter);

        //TODO: aqui podemos agregar el extra
        return $consulta != null ? $consulta[0] : null;
    }
    
    public function getPaymentsByMortgageId($id) {

        $filter = ["loanid" => $id];
        $consulta = $this->getDao()->getAllMortgagePaymentObjs($filter);

        //TODO: aqui podemos agregar el extra
        return $consulta != null ? $consulta[0] : null;
    }

    public function saveMortgage($params) {

        $obj = new Mortgage();

        $update = false;
        if (isset($params["mid"]) && $params["mid"] > 0) {
            $obj = $this->getMortgageById($params["mid"]);
            $update = true;
        } 

        if ($obj == null) { // beacuse mid for web use only
            // object not found
            $obj = new Mortgage();
            $obj->setEstatus($GLOBALS["config"]["status"]["error"]["swal"]);
            $obj->setMessage("El préstamo solicitado no existe");
            return $obj;
            
        }
        
        $status = $GLOBALS["config"]["status"]["mortgage"]["requested"];
        if(isset($params["status"])) {
            $status = $GLOBALS["config"]["status"]["mortgage"][$params["status"]];
        }
        
        $obj->setEstatus($status);
        $obj->setAmount($params["amount"]);
        $obj->setAnnualRate($params["annualrate"]);
        $obj->setDes(date('Ymd'));
        $obj->setWorkerSalary($params["salary"]);
        $obj->setFeeTioSan($params["feetiosan"]);
        $obj->setFeeCost($params["feecost"]);
        
        
        
        //error_log(print_r($obj, true));
        $obj = $this->getDao()->saveObj($obj);

        if ($obj->getId() > 0) {

            $cMail = new MailControl();
            $cMail->sendActivationMessage($obj, $obj->getToken());
            
            $obj->setEstatus($GLOBALS["config"]["status"]["active"]["swal"]);
            $obj->setMessage("El cliente '" . $obj->getDes() . "' fue "
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
     * @return array User
     */
    public function getALLUsers($filter = null) {

        $consulta = $this->getDao()->getAllObjs($filter);

        return $consulta;
    }

    /**
     * All users with pagination
     * @param array $filter
     * @param int $show elements to show
     * @param int $page what page try to show
     * @return array ["total"=><int> , "data"=><array>objects]
     */
    public function getALLUsersPagination($filter = null, $show = 50, $page = 1) {

        $conditional = [];
        if ($filter != null) {
            $conditional = $filter;
        }
        $conditional["show"] = $show;
        $conditional["page"] = ($page - 1) * $show;

        $consulta = [
            "total" => $this->getDao()->getAllObjsCount($conditional),
            "data" => $this->getDao()->getAllObjs($conditional)
        ];

        return $consulta;
    }

    public function delUser($id, $parentuser = "") {

        $obj = $this->getUserById($id, $parentuser);
        if ($obj != null) {

            if ($this->getDao()->delObj($obj) == 1) {
                $obj->setEstatus($GLOBALS["config"]["status"]["active"]["swal"]);
                $obj->setMessage("El usuario '" . $obj->getEmail() . "' ha sido eliminada exitosamente");
            } else {
                $obj->setEstatus($GLOBALS["config"]["status"]["error"]["swal"]);
                $obj->setMessage("Hubo un problema al eliminar el usuario de la base de datos");
            }
        } else {
            $obj = new User();
            $obj->setEstatus($GLOBALS["config"]["status"]["active"]["swal"]);
            $obj->setMessage("El usuario ha eliminar no se encuentra registrado");
        }

        return $obj;
    }

    /**
     * Check user information to access API endpoints
     * @param type $jwtToken
     * @return User | false
     */
    public function validateUserAccess($jwtToken){
        
        $user = false;
        
        try {
            
            $obj = JWT::decode($jwtToken, new Key($GLOBALS["config"]["jwt." . $GLOBALS["config"]["env"]], 'HS256'));
            
            // check if that user exists or enable to access API
            /*[id] => 1
    [name] => admin
    [status] => active
    [type] => admin
    [email] => franciscojgomezl@gmail.com
    [dni] => */
            
            $obj = (array)$obj;
            $filter = ["email" => $obj["email"], "limit" => 1];
            $l = $this->getALLUsers($filter);
            
            if($l == null || count($l) == 0) return false;
            
            //$user = new User();
            $user = $l[0];
            if($user->getEstatus() !== $GLOBALS["config"]['status']["active"]["db"]) return false;
            
        } catch (Exception $err) {
            return false;
        }
        
        return $user;
    }
    
}

?>