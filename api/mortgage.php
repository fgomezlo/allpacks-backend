<?php

error_reporting(E_ALL & ~E_DEPRECATED);

require_once '../php/util/Constants.php';
require_once '../php/domain/Combo.php';
require_once '../php/lib/Rest.inc.php';

require_once '../php/service/connection/mysql.php';

// Users
require_once '../php/service/user/UserDAOImpl.php';
require_once '../php/controller/user/UserControl.php';
require_once '../php/domain/user/User.php';

// Mailing notifications
require_once '../php/controller/mail/MailControl.php';

// JWT autoload
require __DIR__ . '/../vendor/autoload.php';

class Auth extends REST {
    /*
     * Funcion que te permita publicar las funciones del site
     *
     */
    private $curUser;

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
        
        $token = null;
        
        //verifying exists access_token
        if (isset($_SERVER["HTTP_ACCESS_TOKEN"])) {
            $token = $_SERVER['HTTP_ACCESS_TOKEN'];
        } else if (isset($_SERVER["ACCESS_TOKEN"])) {
            $token = $_SERVER['ACCESS_TOKEN'];
        }
        
        if($token == null) {
            $this->response(json_encode(["error" => "403 Unauthorized Request"]), 403);
            exit();
        }

        $userControl = new UserControl();
        if(!($curUser = $userControl->validateUserAccess($token))) {    
            $this->response(json_encode(["error" => "403 Unauthorized Request (not valid token)"]), 403);
            exit();
        }

        /*$this->response(json_encode([
            "message" => "Excelent you send POST method",
            "jbody" => $data,
            "func" => "testing"
                ]), 200);
         
        
        exit;*/

        $func = strtolower(trim(str_replace("/", "", $data["function"])));
        if ((int) method_exists($this, $func) <= 0) {
            $this->response(json_encode(["error" => "Function Not found"]), 404);    // If the method not exist with in this class, response would be "Page not found".
            exit;
        }
        
        $this->$func($data);
        
        exit;
    }

    public function save($data) {

        error_log(print_r($data, true));

        /*
        let payment = { 
        month : i,
        capital: capital,
        monthlyrate : monthlyrate,
        monthlycapital: monthlyquotepayment - monthlyrate,
        monthlypayment: monthlyquotepayment,
        datepayment: `01-${firstdatequota.getMonth()}-${firstdatequota.getFullYear()}`
       };
         *          */
        
        $payment = [
            "month" => 1,
            "capital" => 100,
            "monthlyrate" => 5.66,
            "monthlycapital" => 100,
            "monthlypayment" => 40,
            "datepayment" => "dd/mm/yyyy"
        ];
        
        $arrayResponse = [
            "id" => "id",
            "code" => "code",
            "payments" => [ 
                $payment , $payment
            ]
        ];

        $this->response(json_encode($arrayResponse), 200);
    }
    
    public function list($data) {

        $uControl = new UserControl();
        
        if(!isset($data["email"]) || trim($data["email"]) == "") {
            $this->response(json_encode([
                "error" => "404-1",
                "msg" => "El campo email es necesario",
                "fire" => [
                    "title" => "Ups !!!",
                    "comment" => "El campo email es necesario",
                    "type" => "error"
                    ]
                ]), 403);
            return;
        }
        
        if(!isset($data["name"]) || trim($data["name"]) == "" ) {
            $this->response(json_encode([
                "error" => "404-2",
                "msg" => "El campo nombre es necesario",
                "fire" => [
                    "title" => "Ups !!!",
                    "comment" => "El campo nombre es necesario",
                    "type" => "error"
                    ]
                ]), 403);
            return;
        }
        
        if(!isset($data["dni"]) || trim($data["dni"]) == "" ) {
            $this->response(json_encode([
                "error" => "404-3",
                "msg" => "La cedula de identidad es necesaria",
                "fire" => [
                    "title" => "Ups !!!",
                    "comment" => "La cedula de identidad es necesaria",
                    "type" => "error"
                    ]
                ]), 403);
            return;
        }
        
        /*if(!isset($data["dni"]) || trim($data["dni"]) == "" ) {
            $this->response(json_encode([
                "error" => "404-3",
                "msg" => "La cedula de identidad es necesaria"
                ]), 403);
            return;
        }*/
        
        
        $filter = [ "email" => trim($data["email"])];
        $userlst = $uControl->getALLUsers($filter);

        if($userlst != null) {
            $this->response(json_encode([
                "error" => "404-4",
                "msg" => "El usuario " . $filter["email"] . " ya existe",
                "fire" => [
                    "title" => "Ups !!!",
                    "comment" => "El usuario " . $filter["email"] . " ya existe",
                    "type" => "error"
                    ]
                ]), 403);
            return;
        }        
        
        $user = new User();        
        
        $params = [
            "email" => trim($filter["email"]),
            "status" => "signup",
            "name" => trim($data["name"]),
            "dni" => trim($data["dni"]),
            "type" => "customer"
        ];
        
        $user = $uControl->saveUser($params);

        $arrayResponse = [
            "fire" =>[
                "title" => $user->getMessage(),
                "comment" => ($user->getEstatus() == "success" ? 
                        "Para completar su registro, se ha enviado un correo "
                        . "electrónico ha " . $filter["email"] 
                        . " para completar su activación" : ""),
                "type" => $user->getEstatus()
            ]
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
