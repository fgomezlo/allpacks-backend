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
require_once '../php/controller/mail/MailControl.php';

// JWT autoload
require __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class Auth extends REST {
    /*
     * Funcion que te permita publicar las funciones del site
     *
     */

    public function processApi() {

        // error_log($this->get_request_method());
        if($this->get_request_method() == "OPTIONS") {
            // error_log("first call");
            exit();
        }
        
        if ($this->get_request_method() != "POST" ) {
            //$this->getCustomers($_REQUEST);
            $this->response(json_encode(["error" => "403 Unauthorized Request"]), 403);
            exit();
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        if ($data == null) {
            $data = $this->_request;
            $tmp = json_decode($data, true);
            if($tmp != null) {
                $data = $tmp;
            }
        }
        
        $func = strtolower(trim(str_replace("/", "", $data["function"])));
        if ((int) method_exists($this, $func) <= 0) {
            $this->response(json_encode(["error" => "Function Not found"]), 404);    // If the method not exist with in this class, response would be "Page not found".
            exit;
        }
        
        $this->$func($data);
        
        /*$this->response(json_encode([
            "message" => "Excelent you send POST method",
            "jbody" => $data,
            "func" => $_REQUEST
                ]), 200);
         */
        
        exit;
    }

    public function signin($data) {

        $uControl = new UserControl();
        
        if(!isset($data["loguser"]) || trim($data["loguser"]) == "") {
            $this->response(json_encode([
                "error" => "403-1",
                "msg" => "El campo Usuario es necesario"
                ]), 403);
            return;
        }
        
        if(!isset($data["password"]) || trim($data["password"]) == "" ) {
            $this->response(json_encode([
                "error" => "403-2",
                "msg" => "El campo password es necesario"
                ]), 403);
            return;
        }
        
        
        $filter = [ 
            "loguser" => trim($data["loguser"]),
            "getroles" => true
        ];
        $userlst = $uControl->getALLUsers($filter);

        if($userlst == null) {
            $this->response(json_encode([
                "error" => "403-3",
                "msg" => "El usuario " . $filter["email"] . " no existe"
                ]), 403);
            return;
        }        
        
        $user = new User();
        $user = $userlst[0];

        if($user->getEstatus() != "1") {
            $this->response(json_encode([
                "error" => "403-4",
                "msg" => "El usuario " . $filter["email"] . " no se encuentra activo"
                ]), 403);
            return;
        }
        
        if($user->getPassword() != md5($data["password"])) {
            $this->response(json_encode([
                "error" => "403-5",
                "msg" => "Clave inválida "
                ]), 403);
            return;
        }
        
        
        $token = $user->getJSONobject();

        $arrayResponse = [
            "token" => JWT::encode($token, $GLOBALS["config"]["jwt." . $GLOBALS["config"]["env"]], 'HS256')
        ];

        $this->response(json_encode($arrayResponse), 200);
    }
 
}

// Initiiate Library
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
/*error_log(file_get_contents('php://input'));
error_log(print_r($_REQUEST, true));
error_log(print_r($_SERVER, true));*/
$api = new Auth();
$api->processApi();
