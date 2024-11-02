<?php

error_reporting(E_ALL & ~E_DEPRECATED);

require_once '../php/util/Constants.php';
require_once '../php/domain/Combo.php';
require_once '../php/lib/Rest.inc.php';

require_once '../php/service/connection/mysql.php';

// Users
require_once '../php/service/user/UserDAOImpl.php';
require_once '../php/service/user/RolDAOImpl.php';
require_once '../php/controller/user/UserControl.php';
require_once '../php/domain/user/User.php';
require_once '../php/domain/user/Rol.php';

// Mailing notifications
require_once '../php/controller/mail/AuthMailControl.php';

// JWT autoload
require __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth extends REST {
    /*
     * Funcion que te permita publicar las funciones del site
     *
     */

    private $currentUser;
    
    public function processApi() {

        // error_log($this->get_request_method());
        if ($this->get_request_method() == "OPTIONS") {
            // error_log("first call");
            exit();
        }
        
        if ($this->get_request_method() != "POST") {
            //$this->getCustomers($_REQUEST);
            $this->response(json_encode(["error" => "403 Unauthorized Request"]), 403);
            exit();
        }
        
        if (isset($_SERVER["HTTP_ACCESS_TOKEN"])) {
            $token = $_SERVER['HTTP_ACCESS_TOKEN'];
        } else if (isset($_SERVER["ACCESS_TOKEN"])) {
            $token = $_SERVER['ACCESS_TOKEN'];
        }

        if ($token == null) {
            $this->response(json_encode(["error" => "403 Unauthorized Request"]), 403);
            exit();
        }
        
        // check user is enable
        if(!$this->checkUserToken($token)) {
            $this->response(json_encode(["error" => "Usuario no autorizado para esta solicitud"]), 403);
            exit();
        } 

        $data = json_decode(file_get_contents('php://input'), true);
        if ($data == null) {
            $data = $this->_request;
            $tmp = json_decode($data, true);
            if ($tmp != null) {
                $data = $tmp;
            }
        }

        $func = strtolower(trim(str_replace("/", "", $data["function"])));
        if ((int) method_exists($this, $func) <= 0) {
            $this->response(json_encode(["error" => "Function Not found"]), 404);    // If the method not exist with in this class, response would be "Page not found".
            exit;
        }

        $this->$func($data);

        exit;
    }

    private function checkUserToken($jwtToken) {

        $uControl = new UserControl();
        $this->currentUser = $uControl->validateUserAccess($jwtToken);
        
        if($this->currentUser == false) {
            return false;
        }

        /** Add other custom validations for this api */
        return true;
    }

    public function search($data) {
        
        $offset = 0;
        if(isset($data["offset"])) {
            $offset = $data["offset"];
        }
        
        $limit = $GLOBALS["config"]["limit"];
        if(isset($data["limit"])) {
            $limit = $data["limit"];
        }
        
        $uControl = new UserControl();

        $filter = [
            "filtervalue" => trim($data["filtervalue"]),
            "getroles" => true
        ];
        $userlst = $uControl->getALLUsersPagination($filter, $limit, $offset);
        $useritems = [];
        if($userlst["data"] != null) {
            foreach ($userlst["data"] as $value) {
                //$value = new User();
                $useritems[] = $value->getJSONobject();
            }
        } 
        
        $arrayResponse = [
            "totalitems" => $userlst["total"],
            "items" => $useritems,
            "offset" => $offset,
            "limit" => $limit,
        ];

        $this->response(json_encode($arrayResponse), 200);
    }
    
    public function saveuser($data) {
        
        $isupdate = isset($data["id"]) && $data["id"] > 0;

        $uControl = new UserControl();
        $olduser = null;
        if($isupdate){
            $lstuser = $uControl->getALLUsers([
                "id" => $data["id"],
                "getroles" => true
            ]);
            
            if($lstuser == null) {
                $this->response(json_encode([
                "error" => "500-1",
                "msg" => "Problemas en la plataforma de allpacks<br />Intente más tarde"
                    ]), 404);
                return;
            }
            $olduser = $lstuser[0];
        }
        
        $params = [
            "email" => $data["email"],
            "status" => $data["status"] == 1 ? "active" : "disabled", // active | disabled
            "name" => $data["name"],
            "loguser" => $data["dni"]
        ];
        
        
        // check email
        $lst = $uControl->getALLUsers(["email" => $data["email"]]);
        if($lst != null && $lst[0]->getId() != $data["id"]) {
             $this->response(json_encode([
            "error" => "500-2",
            "msg" => "El correo ha sido utilizado en otra cuenta"
                ]), 403);
            return;
        }

        // check loguser
        $lst = $uControl->getALLUsers(["loguser" => $data["dni"]]);
        if($lst != null && $lst[0]->getId() != $data["id"]) {
            $this->response(json_encode([
            "error" => "500-3",
            "msg" => "El nombre de usuario: \"". $data["dni"] . "\" ha sido utilizado en otra cuenta"
                ]), 403);
            return;
        }
        
        
        if(isset($data["password"]) && $data["password"] != "") {
           $params["password"] = $data["password"];
        }

        $savedUser = $uControl->saveUser($params, $olduser);
        if(isset($data["roles"]) && count($data["roles"]) > 0) {
            
            $savedRoles = [];
            foreach ($data["roles"] as $rol) {                
                // create list of roles and put it in role user list
                $savedRoles[] = $rol["id"];
            }

            $savedUser = $uControl->saveUserRol($savedUser, $savedRoles);
        } 

        $arrayResponse = [
            "code" => $savedUser->getEstatus(),
            "message" => $savedUser->getMessage(),
            "data" => $savedUser->getJSONobject()
        ];
        
        $this->response(json_encode($arrayResponse), 200);
    }
    
    public function rollist($data) {
        
        $uControl = new UserControl();
        $lst = $uControl->getRoles();
        $lstobj = [];
        if($lst != null) {
            foreach ($lst as $value) {
                $lstobj[] = $value->getJSONobject();
            }
        }
         
        $arrayResponse = [
            "totalitems" => count($lstobj),
            "items" => $lstobj,
        ];
        
        $this->response(json_encode($arrayResponse), 200);
        
    }

    public function deluser($data) {
        
        if(!isset($data["id"]) || !$data["id"] > 0) {
            $this->response(json_encode([
                "error" => "510-1",
                "msg" => "Falta id de usuario para eliminar en base de datos"
                    ]), 403);
                return;
        }
        
        $uControl = new UserControl();
        $userdel = $uControl->getUserById($data["id"]);
        if($userdel == null) {
            $this->response(json_encode([
                "error" => "510-2",
                "msg" => "Usuario no existe en la base de datos"
                    ]), 403);
                return;
        }
        
        $response = $uControl->delUser($userdel);
        
        $arrayResponse = [
            "code" => $response->getEstatus(),
            "message" => $response->getMessage(),
            "data" => $userdel->getJSONobject()
        ];
        $this->response(json_encode($arrayResponse), 200);
        
    }
}

// Initiiate Library
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
/* error_log(file_get_contents('php://input'));
  error_log(print_r($_REQUEST, true));
  error_log(print_r($_SERVER, true)); */
$api = new Auth();
$api->processApi();
