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

    public function saveUser($params, $userupdate) {

        $obj = new User();

        $update = false;
        if ($userupdate != null) {
            $obj = $userupdate;
            $update = true;
        } 

        $obj->setEmail($params["email"]);
        $obj->setEstatus(isset($params["status"]) ? $GLOBALS["config"]["status"][$params["status"]]["db"] : $GLOBALS["config"]["status"]["disable"]["db"] );
        $obj->setDes(isset($params["name"]) ? $params["name"] : null);
        $obj->setDni(isset($params["loguser"]) ? $params["loguser"] : null);
        
        if(isset($params["password"])) {
            $obj->setPassword(md5($params["password"]));
        }
        
        /* signup functionality to validate user
         * if($params["status"] == "signup") {
            // generate an activation token
            $obj->setToken(md5($obj->getEmail() . date('YmdHis')));
        }*/
        
        //error_log(print_r($obj, true));
        $obj = $this->getDao()->saveObj($obj);

        if ($obj->getId() > 0) {

            /*$cMail = new MailControl();
            $cMail->sendActivationMessage($obj, $obj->getToken());
            */
            
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
        
        $consulta = $this->addRolesToUserObj($consulta, $filter);
        
        return $consulta;
    }
    
    public function addRolesToUserObj($userarray, $filter) {
        
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
    }

    /**
     * All users with pagination
     * @param array $filter
     * @param int $show elements to show
     * @param int $page what page try to show
     * @return array ["total"=><int> , "data"=><array>objects]
     */
    public function getALLUsersPagination($filter = null, $show = 50, $offset = 0) {

        $conditional = [];
        if ($filter != null) {
            $conditional = $filter;
        }
        $conditional["show"] = $show;
        $conditional["offset"] = $offset;

        $items = $this->getDao()->getAllObjs($conditional);
        $data = $this->addRolesToUserObj($items, $filter);
        
        $consulta = [
            "total" => $this->getDao()->getAllObjsCount($conditional),
            "data" => $data
        ];

        return $consulta;
    }

    public function delUser($obj) {

        if ($obj != null) {

            if ($this->getDao()->delObj($obj) == 1) {
                $obj->setEstatus($GLOBALS["config"]["status"]["active"]["swal"]);
                $obj->setMessage("El usuario '" . $obj->getEmail() . "' ha sido eliminado exitosamente");
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
    
    public function saveUserRol(User $user, $roles) {
        
        $lstRoles = $user->getLstRoles();
        $userrolesid = [];
        $userroles = [];
        
        $delRoles = [];
        if($lstRoles != null) {
            foreach ($lstRoles as $oldRol) {
                if(array_search($oldRol->getId(), $roles) === false) {
                    $delRoles[] = $oldRol;
                } else {
                    $userrolesid[] = $oldRol->getId();
                }
            }
        }
        
        // deleting rol doesnt need anymore
        foreach ($delRoles as $delRol) {
            $this->getDao()->deleteRolToUser($user->getId(), $delRol);
        }
        
        // insert rol to user
        
        foreach ($roles as $idRol) {
            
            $rol = new Rol();
            $rol->setId($idRol);
            $userroles[] = $rol;
            
            // check if rol already exists
            if(array_search($idRol, $userrolesid) === false){
                $this->getDao()->addRolToUser($user->getId(), $rol);
            }
        }
        
        // update roles list for user updated
        $user->setLstRoles($userroles);
        
        return $user;
    }
    
    
    public function getRoles() {
        return $this->getDaoRol()->getAllObjs();
    }
}

?>