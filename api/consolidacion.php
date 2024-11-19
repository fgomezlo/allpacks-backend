<?php

error_reporting(E_ALL & ~E_DEPRECATED);

require_once '../php/util/Constants.php';
require_once '../php/domain/Combo.php';
require_once '../php/lib/Rest.inc.php';

require_once '../php/service/connection/mysql.php';

// Users only for security reasons
require_once '../php/service/user/UserDAOImpl.php';
require_once '../php/service/user/RolDAOImpl.php';
require_once '../php/controller/user/UserControl.php';
require_once '../php/domain/user/User.php';
require_once '../php/domain/user/Rol.php';


//Required files 
require_once '../php/service/consolidacion/ConsolidacionDAOImpl.php';
require_once '../php/service/consolidacion/ConsolidacionItemDAOImpl.php';
require_once '../php/controller/consolidacion/ConsolidacionControl.php';
require_once '../php/domain/consolidacion/Consolidacion.php';
require_once '../php/domain/consolidacion/ConsolidacionItem.php';
require_once '../php/service/consolidacion/EstadoDAOImpl.php';
require_once '../php/domain/customer/Estado.php';

// customer require
require_once '../php/service/customer/CustomerDAOImpl.php';
require_once '../php/controller/customer/CustomerControl.php';
require_once '../php/domain/customer/Customer.php';


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
        
        $text = file_get_contents('php://input');
        //error_log($text);
        $data = json_decode($text, true);
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

        $cControl = new UserControl();
        $this->currentUser = $cControl->validateUserAccess($jwtToken);
        
        if($this->currentUser == false) {
            return false;
        }

        /** Add other custom validations for this api */
        return true;
    }

    
    public function tracking($data) {
        $offset = 0;
        if(isset($data["offset"])) {
            $offset = $data["offset"];
        }
        
        $limit = $GLOBALS["config"]["limit"];
        if(isset($data["limit"])) {
            $limit = $data["limit"];
        }
        
        // error_log(print_r($data, true));
        
        $cControl = new ConsolidacionControl();

        $filter = [
            "getitems" => true,
            "status" => "1, 2, 5" // pendiente, en proceso, reempacado
        ];
        
        if(isset($data["filtervalue"]) && $data["filtervalue"] != null) {
            $filter["tracking"] = $data["filtervalue"];
        }
        
        $objlst = $cControl->getALLConsolidacionPagination($filter, $limit, $offset);
        $objitems = [];
        if($objlst["data"] != null) {
            
            $uControl = new UserControl();
            $cusControl = new CustomerControl();
            
            foreach ($objlst["data"] as $value) {
                //$value = new Consolidacion();
                
                // Customer full object
                if($value->getIdcliente() != null ) {
                    $value->setIdcliente($cusControl->getCustomerById($value->getIdcliente()));
                }
                
                if($value->getIdusuario() != null ) {
                    $value->setIdusuario($uControl->getUserById($value->getIdusuario()));
                }
                
                $objitems[] = $value->getJSONobject();
            }
        } 
        
        $arrayResponse = [
            "totalitems" => $objlst["total"],
            "items" => $objitems,
            "offset" => $offset,
            "limit" => $limit,
        ];

        $this->response(json_encode($arrayResponse), 200);
        
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
        
        // error_log(print_r($data, true));
        
        $cControl = new ConsolidacionControl();

        $filter = [
            "getitems" => true
        ];
        
        // load filter parameters
        if(isset($data["tiposervicio"]) && $data["tiposervicio"] != null) {
            $filter["tiposervicio"] = $data["tiposervicio"];
        }
        
        if( isset($data["statusconsolidacion"]) && $data["statusconsolidacion"] != null){
            
            $stridsbycoma = "";
            foreach ($data["statusconsolidacion"] as $status) {
                $stridsbycoma .=  ($stridsbycoma != "" ? "," : "") . $status["id"];
            }
            
            $filter["status"] = $stridsbycoma;
        }
        
        // check range dates
        if(isset($data["dpFrom"]) && $data["dpFrom"] != null) {
            $filter["fromdate"] = $data["dpFrom"]["year"] . "-" . 
                    $data["dpFrom"]["month"] . "-" . $data["dpFrom"]["day"];
        }
        
        if(isset($data["dpTo"]) && $data["dpTo"] != null) {
            $filter["todate"] = $data["dpTo"]["year"] . "-" . 
                    $data["dpTo"]["month"] . "-" . $data["dpTo"]["day"];
        }
        
        if(isset($data["filtervalue"]) && $data["filtervalue"] != null) {
            $filter["filtervalue"] = $data["filtervalue"];
        }
        
        $objlst = $cControl->getALLConsolidacionPagination($filter, $limit, $offset);
        $objitems = [];
        if($objlst["data"] != null) {
            
            $uControl = new UserControl();
            $cusControl = new CustomerControl();
            
            foreach ($objlst["data"] as $value) {
                //$value = new Consolidacion();
                
                // Customer full object
                if($value->getIdcliente() != null ) {
                    $value->setIdcliente($cusControl->getCustomerById($value->getIdcliente()));
                }
                
                if($value->getIdusuario() != null ) {
                    $value->setIdusuario($uControl->getUserById($value->getIdusuario()));
                }
                
                $objitems[] = $value->getJSONobject();
            }
        } 
        
        $arrayResponse = [
            "totalitems" => $objlst["total"],
            "items" => $objitems,
            "offset" => $offset,
            "limit" => $limit,
        ];

        $this->response(json_encode($arrayResponse), 200);
    }
    
    public function saveinstruccion($data) {
        
        $isupdate = isset($data["id"]) && $data["id"] > 0;

        $cControl = new CustomerControl();
        $oldcustomer = null;
        if($isupdate){
            $lstcustomer = $cControl->getALLCustomers([
                "id" => $data["id"]
            ]);
            
            if($lstcustomer == null) {
                $this->response(json_encode([
                "error" => "520-1",
                "msg" => "Problemas en la plataforma de allpacks<br />Intente más tarde"
                    ]), 404);
                return;
            }
            $oldcustomer = $lstcustomer[0];
        }
        
        $params = [
            "status" => $data["status"] == 1 ? "active" : "disabled", // active | disabled
            "name" => $data["name"],
            "email" => $data["email"],
            "empresa" => $data["empresa"],
            "direccion" => $data["direccion"],
            "ciudad" => $data["ciudad"],
            "estado" => $data["estado"],
            "pais" => $data["pais"],
            "destino" => $data["destino"],
            "telefono" => $data["telefono"],
            "notas" => $data["notas"],
            "codigo" => $data["codigo"],
            "sync" => isset($data["sync"]) ? $data["sync"] : 0
        ];
        
        
        // check email
        $lst = $cControl->getALLCustomers(["email" => $data["email"]]);
        if($lst != null && $lst[0]->getId() != $data["id"]) {
             $this->response(json_encode([
            "error" => "520-2",
            "msg" => "El correo ha sido utilizado en otro cliente"
                ]), 403);
            return;
        }

        // check codigo
        $lst = $cControl->getALLCustomers(["codigo" => $data["codigo"]]);
        if($lst != null && $lst[0]->getId() != $data["id"]) {
            $this->response(json_encode([
            "error" => "520-3",
            "msg" => "El codigo cliente: \"". $data["codigo"] . "\" ha sido utilizado en otro cliente"
                ]), 403);
            return;
        }
        
        
        if(isset($data["password"]) && $data["password"] != "") {
           $params["password"] = $data["password"];
        }

        $savedCustomer = $cControl->saveCustomer($params, $oldcustomer);

        $arrayResponse = [
            "code" => $savedCustomer->getEstatus(),
            "message" => $savedCustomer->getMessage(),
            "data" => $savedCustomer->getJSONobject()
        ];
        
        $this->response(json_encode($arrayResponse), 200);
    }

    public function delconsolidacion($data) {
        
        if(!isset($data["id"]) || !$data["id"] > 0) {
            $this->response(json_encode([
                "error" => "709-1",
                "msg" => "Falta id de consolidacion para eliminar en base de datos"
                    ]), 403);
                return;
        }
        
        $cControl = new ConsolidacionControl();
        $lstconsolidacion = $cControl->getALLConsolidacion(["id" => $data["id"]] );
        if($lstconsolidacion == null) {
            $this->response(json_encode([
                "error" => "709-2",
                "msg" => "La consolidacion no existe en la base de datos"
                    ]), 403);
                return;
        }
        
        $customerdel = $lstconsolidacion[0];
        $response = $cControl->delConsolidacion($customerdel);
        
        $arrayResponse = [
            "code" => $response->getEstatus(),
            "message" => $response->getMessage(),
            "data" => $customerdel->getJSONobject()
        ];
        $this->response(json_encode($arrayResponse), 200);
        
    }
    
    public function changestatusconsolidacion($data) {
        
        if(!isset($data["id"]) || !$data["id"] > 0) {
            $this->response(json_encode([
                "error" => "710-1",
                "msg" => "Falta id de consolidacion para actualizar estatus"
                    ]), 403);
                return;
        }
        
        if(!isset($data["status"]) || !$data["status"] > 0) {
            $this->response(json_encode([
                "error" => "710-2",
                "msg" => "Falta status para actualizar la consolidacion"
                    ]), 403);
                return;
        }
        
        $cControl = new ConsolidacionControl();
        $lstconsolidacion = $cControl->getALLConsolidacion(["id" => $data["id"]] );
        if($lstconsolidacion == null) {
            $this->response(json_encode([
                "error" => "710-3",
                "msg" => "La consolidacion no existe en la base de datos"
                    ]), 403);
                return;
        }
        //$consolidacionupdated = new Consolidacion();
        $consolidacionupdated = $lstconsolidacion[0];
        $params = [
            "status" => $data["status"],
            "nota" => $consolidacionupdated->getNota(),
            "observacion" => $consolidacionupdated->getObservacion(),
            "tiposervicio" => $consolidacionupdated->getTipoServicio(),
            "cliente" => [ "id" => $consolidacionupdated->getIdcliente()],
            "usuario" => ["id" => $this->currentUser->getId() ]
        ];
        
        $response = $cControl->saveConsolidacion($params, $consolidacionupdated);
        
        $arrayResponse = [
            "code" => $response->getEstatus(),
            "message" => $response->getMessage(),
            "data" => $consolidacionupdated->getJSONobject()
        ];
        $this->response(json_encode($arrayResponse), 200);
        
    }
    
    public function savetracking($data) {
        
        if(!isset($data["id"]) || !$data["id"] > 0) {
            $this->response(json_encode([
                "error" => "711-1",
                "msg" => "Falta id de consolidacion para actualizar estatus"
                    ]), 403);
                return;
        }
        
        if(!isset($data["paquetes"]) || !$data["paquetes"] > 0) {
            $this->response(json_encode([
                "error" => "711-2",
                "msg" => "Falta paquetes ha actualizar"
                    ]), 403);
                return;
        }
        
        $cControl = new ConsolidacionControl();
        $lstconsolidacion = $cControl->getALLConsolidacion(["id" => $data["id"]] );
        if($lstconsolidacion == null) {
            $this->response(json_encode([
                "error" => "711-3",
                "msg" => "La consolidacion no existe en la base de datos"
                    ]), 403);
                return;
        }
        
        
        //$consolidacionupdated = new Consolidacion();
        $consolidacionupdated = $lstconsolidacion[0];
        $response = $cControl->saveTrackingInfo($data["paquetes"], $consolidacionupdated);
        
        $arrayResponse = [
            "code" => $response->getEstatus(),
            "message" => $response->getMessage(),
            "data" => $consolidacionupdated->getJSONobject()
        ];
        $this->response(json_encode($arrayResponse), 200);
        
    }
    
    
    public function estadolist($data) {
        
        $cControl = new ConsolidacionControl();
        $lst = $cControl->getEstados();
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
}

// Initiiate Library
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
/* error_log(file_get_contents('php://input'));
  error_log(print_r($_REQUEST, true));
  error_log(print_r($_SERVER, true)); */
$api = new Auth();
$api->processApi();
