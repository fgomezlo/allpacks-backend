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


        $data = json_decode(file_get_contents('php://input'), true);
        if ($data == null) {
            $data = $this->_request;
            $tmp = json_decode($data, true);
            if ($tmp != null) {
                $data = $tmp;
            }
        }

        /*if (!isset($data["test"]) || !$data["test"]) {

            if (!isset($data["recaptcha"])) {
                $this->response(json_encode(["error" => "recaptcha Not found"]), 404);    // If the method not exist with in this class, response would be "Page not found".
                exit;
            }

            $url = 'https://www.google.com/recaptcha/api/siteverify';
            $dataRecaptcha = array('secret' => $GLOBALS["config"]["recaptcha.secret-server"], 'response' => $data["recaptcha"]);

            // use key 'http' even if you send the request to https://...
            $options = array(
                'http' => array(
                    'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method' => 'POST',
                    'content' => http_build_query($dataRecaptcha)
                )
            );

            $context = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
            $dataResult = json_decode($result, true);
            if ($dataResult === FALSE || !isset($dataResult["success"]) || !$dataResult["success"]) { /* Handle error 
                $this->response(json_encode(["error" => "recaptcha not validated"]), 404);    // If the method not exist with in this class, response would be "Page not found".
                exit;
            }
        } */
        $func = strtolower(trim(str_replace("/", "", $data["function"])));
        if ((int) method_exists($this, $func) <= 0) {
            $this->response(json_encode(["error" => "Function Not found"]), 404);    // If the method not exist with in this class, response would be "Page not found".
            exit;
        }

        $this->$func($data);

        /* $this->response(json_encode([
          "message" => "Excelent you send POST method",
          "jbody" => $data,
          "func" => $_REQUEST
          ]), 200);
         */

        exit;
    }

    private function recaptcha($data) {

        if (isset($data["test"]) && $data["test"])
            return true;

        if (!isset($data["recaptcha"])) {
//                $this->response(json_encode(["error" => "recaptcha Not found"]), 404);    // If the method not exist with in this class, response would be "Page not found".
//                exit;
            return false;
        }

        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $dataRecaptcha = array('secret' => $GLOBALS["config"]["recaptcha.secret-server"], 'response' => $data["recaptcha"]);

        // use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($dataRecaptcha)
            )
        );

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $dataResult = json_decode($result, true);
        if ($dataResult === FALSE || !isset($dataResult["success"]) || !$dataResult["success"]) { /* Handle error */
//                $this->response(json_encode(["error" => "recaptcha not validated"]), 404);    // If the method not exist with in this class, response would be "Page not found".
//                exit;
            return false;
        }


        return true;
    }

    public function signin($data) {
        
        if(!$this->recaptcha($data)) {
            $this->response(json_encode([
                "error" => "403-100",
                "msg" => "Problemas con el recaptcha"
                    ]), 403);
        }

        $uControl = new UserControl();

        if (!isset($data["loguser"]) || trim($data["loguser"]) == "") {
            $this->response(json_encode([
                "error" => "403-1",
                "msg" => "El campo Usuario es necesario"
                    ]), 403);
            return;
        }

        if (!isset($data["password"]) || trim($data["password"]) == "") {
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

        if ($userlst == null) {
            $this->response(json_encode([
                "error" => "403-3",
                "msg" => "El usuario " . $filter["loguser"] . " no existe"
                    ]), 403);
            return;
        }

        $user = new User();
        $user = $userlst[0];

        if ($user->getEstatus() != "1") {
            $this->response(json_encode([
                "error" => "403-4",
                "msg" => "El usuario " . $filter["loguser"] . " no se encuentra activo"
                    ]), 403);
            return;
        }

        if ($user->getPassword() != md5($data["password"])) {
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

    public function passrecovery($data) {
        
        if(!$this->recaptcha($data)) {
            $this->response(json_encode([
                "error" => "403-100",
                "msg" => "Problemas con el recaptcha"
                    ]), 403);
        }

        $uControl = new UserControl();

        if (!isset($data["email"]) || trim($data["email"]) == "") {
            $this->response(json_encode([
                "error" => "404-1",
                "msg" => "El campo Correo es necesario"
                    ]), 403);
            return;
        }


        $filter = [
            "email" => trim($data["email"])
        ];
        $userlst = $uControl->getALLUsers($filter);

        if ($userlst == null) {
            $this->response(json_encode([
                "error" => "404-2",
                "msg" => "El correo " . $filter["email"] . " no existe"
                    ]), 403);
            return;
        }

        $user = new User();
        $user = $userlst[0];

        if ($user->getEstatus() != "1") {
            $this->response(json_encode([
                "error" => "404-3",
                "msg" => "El correo " . $filter["email"] . " no se encuentra activo"
                    ]), 403);
            return;
        }

        // create token reset for 
        $linkActivateToken = $uControl->generateResetPasswordToken($user);


        // Do send traditional send mail over tls
        $mailControl = new AuthMailControl();
        $sendMail = $mailControl->sendRecoveryPass($user, $linkActivateToken);

        if (!$sendMail) {
            $this->response(json_encode([
                "error" => "404-4",
                "msg" => "Inconvenientes con la plataforma de correos allpacksfc.com"
                    ]), 403);
            return;
        }

        $arrayResponse = [
            "message" => "Sent Mail"
        ];

        $this->response(json_encode($arrayResponse), 200);
    }
    
    public function checkToken($data) {

        $uControl = new UserControl();

        if (!isset($data["token"]) || trim($data["token"]) == "") {
            $this->response(json_encode([
                "error" => "405-1",
                "msg" => "El campo token es necesario"
                    ]), 403);
            return;
        }


        $filter = [
            "tokenreset" => trim($data["token"])
        ];
        $userlst = $uControl->getALLUsers($filter);

        if ($userlst == null) {
            $this->response(json_encode([
                "error" => "405-2",
                "msg" => "El token está caducado o no existe"
                    ]), 403);
            return;
        }

        $user = new User();
        $user = $userlst[0];

        if ($user->getEstatus() != "1") {
            $this->response(json_encode([
                "error" => "405-3",
                "msg" => "El token se encuentra inactivo porque el usuario está suspendido"
                    ]), 403);
            return;
        }

        $arrayResponse = [
            "data" => [
                "name" => $user->getDes(),
                "email" => $user->getEmail()
            ]
        ];

        $this->response(json_encode($arrayResponse), 200);
    }
    
    public function resetpass($data) {

        if(!$this->recaptcha($data)) {
            $this->response(json_encode([
                "error" => "403-100",
                "msg" => "Problemas con el recaptcha"
                    ]), 403);
        }
        
        $uControl = new UserControl();

        if (!isset($data["token"]) || trim($data["token"]) == "") {
            $this->response(json_encode([
                "error" => "406-1",
                "msg" => "El campo token es necesario"
                    ]), 403);
            return;
        }
        
        if (!isset($data["pass"]) || trim($data["pass"]) == "") {
            $this->response(json_encode([
                "error" => "406-2",
                "msg" => "El campo clave es necesario y no puede ser vacio"
                    ]), 403);
            return;
        }


        $filter = [
            "tokenreset" => trim($data["token"])
        ];
        $userlst = $uControl->getALLUsers($filter);

        if ($userlst == null) {
            $this->response(json_encode([
                "error" => "406-2",
                "msg" => "El token está caducado o no existe"
                    ]), 403);
            return;
        }

        $user = new User();
        $user = $userlst[0];

        if ($user->getEstatus() != "1") {
            $this->response(json_encode([
                "error" => "406-3",
                "msg" => "El token se encuentra inactivo porque el usuario está suspendido"
                    ]), 403);
            return;
        }
        
        // call reset pass to update onli password
        $uControl->resetPassword($user, $data["pass"]);

        $arrayResponse = [
            "message" => "Clave reestablecida exitosamente"
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
