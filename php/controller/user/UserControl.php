<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class UserControl {

    /** @var  UserDAOImpl */
    private $dao;
    private $daoRol;

    private function getDao() {
        if ($this->dao == null) {
            $this->dao = new UserDAOImpl();
        }

        return $this->dao;
    }
    
    private function getDaoRol() {
        if ($this->daoRol == null) {
            $this->daoRol = new RolDAOImpl();
        }

        return $this->daoRol;
    }

    /**
     * 
     * @param type $id
     * @return Tag
     */
    public function getUserById($id) {

        $filter = ["id" => $id];
        $consulta = $this->getDao()->getAllObjs($filter);

        //TODO: aqui podemos agregar el extra
        return $consulta != null ? $consulta[0] : null;
    }

    public function saveUser($params) {

        $obj = new User();

        $update = false;
        if (isset($params["mid"]) && $params["mid"] > 0) {
            $obj = $this->getUserById($params["mid"]);
            $update = true;
        } else if (isset($params["email"]) && $params["email"] != "") {
            $filter = [
                "email" => $params['email']
            ];
            
            $response = $this->getALLUsers($filter);
            if($response != null) {
                return $response[0];
            }

        }

        if ($obj == null) { // beacuse mid for web use only
            // object not found
            $obj = new User();
            $obj->setEstatus($GLOBALS["config"]["status"]["error"]["swal"]);
            $obj->setMessage("El usuario no existe");
            return $obj;
            
        }

        $obj->setEmail($params["email"]);
        $obj->setEstatus(isset($params["status"]) ? $GLOBALS["config"]["status"][$params["status"]]["db"] : $GLOBALS["config"]["status"]["disable"]["db"] );
        $obj->setDes(isset($params["name"]) ? $params["name"] : null);
        
        /* signup functionality to validate user
         * if($params["status"] == "signup") {
            // generate an activation token
            $obj->setToken(md5($obj->getEmail() . date('YmdHis')));
        }*/
        
        //error_log(print_r($obj, true));
        $obj = $this->getDao()->saveObj($obj);

        if ($obj->getId() > 0) {

            $cMail = new MailControl();
            $cMail->sendActivationMessage($obj, $obj->getToken());
            
            $obj->setEstatus($GLOBALS["config"]["status"]["active"]["swal"]);
            $obj->setMessage("El usuario '" . $obj->getDes() . "' fue "
                    . ($update ? "actualizado" : "agregado")
                    . " exitosamente");
        } else {
            // problemas de bd
            $obj->setEstatus($GLOBALS["config"]["status"]["error"]["swal"]);
            $obj->setMessage("Hubo un problema al actualizar o insertar el usuario en la BD");
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

        if($consulta == null || count($consulta) == 0) 
            return $consulta;
        
        if(isset($filter["getroles"]) && $filter["getroles"]) {
            
            for($i = 0; $i < count($consulta); $i++) {
                
                $usertmp = $consulta[$i];
                $filterRol = [
                    "rolbyuserid" => $usertmp->getId()
                ];
                
                $usertmp->setLstRoles(
                        $this->getDaoRol()->getAllObjs($filterRol)
                        );
                
                $consulta[$i] = $usertmp;
                
            }
            
        }
        
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
    
    public function generateResetPasswordToken(User $user){
        
        $tokenActivate = md5($user->getId().date('Ymdhis'));
        $user->setTokenReset($tokenActivate);
        $this->getDao()->saveObj($user);   
        
        return $tokenActivate;
    }
    
    public function resetPassword(User $user, $password){
        
        $md5sumPass = md5($password);
        $user->setPassword($md5sumPass);
        $user->setTokenReset(null);
        
        return $this->getDao()->saveObj($user);
    }
    
    
}

?>