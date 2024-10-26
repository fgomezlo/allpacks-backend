<?php

require_once '../php/util/Constants.php';
require_once '../php/domain/Combo.php';
require_once '../php/lib/Rest.inc.php';

require_once '../php/service/connection/mysql.php';

// Users
require_once '../php/service/user/UserDAOImpl.php';
require_once '../php/controller/user/UserControl.php';
require_once '../php/domain/user/User.php';

class Api extends REST {
    
    /*
     * Funcion que te permita publicar las funciones del site
     *
     */

    public function processApi() {

        $env = $GLOBALS["config"]["env"];
        $token = null;
        
        //verifying exists access_token
        if (isset($_SERVER["HTTP_ACCESS_TOKEN"])) {
            $token = $_SERVER['HTTP_ACCESS_TOKEN'];
        } else if (isset($_SERVER["ACCESS_TOKEN"])) {
            $token = $_SERVER['ACCESS_TOKEN'];
        }

        if ($token == null || $GLOBALS["config"]["access_token"][$env] != $token) {

            $this->response(json_encode(["error" => "403 Unauthorized Request"]), 403);
            exit();
        }
        
        if ($this->get_request_method() == "GET") {
            //$this->getCustomers($_REQUEST);
            $this->response(json_encode(
                    [
                        "message" => "Excelent you send GET method",
                        "func" => $_REQUEST
                    ]), 200);
            exit;
        }

        if ($this->get_request_method() == "POST" || $this->get_request_method() == "DELETE") {
            
            $data = json_decode(file_get_contents('php://input'), true);
	    if($data == null) {
	    	$data = $_REQUEST;
	    }
	    
	    
	    $func = strtolower(trim(str_replace("/", "", $data["function"])));
	    
	    
	    
	    if ((int) method_exists($this, $func) > 0) {
                $this->$func($data);
            }
            
            $this->response(json_encode([
                "message" => "Excelent you send POST method",
                "jbody" => $data,
                "func" => $_REQUEST
                    ]), 200);
            exit;
        }

        $this->response(json_encode(["error" => "Function Not found"]), 404);    // If the method not exist with in this class, response would be "Page not found".
        exit;
    }
    
    public function saveDataFromJApp($data) {

        $cControl = new ColaboradorControl();
        $gControl = new GerenciaControl();
        $lControl = new LocationControl();
        $dControl = new DeviceControl();
        
        $asignado = null;
        if(isset($data["asignado"]) && $data["asignado"] != null) {
            $asignado = $cControl->saveColaborador([
               "name" => $data["asignado"],
               "status" => "active"     
            ]);
        }
        
        $gerencia = null;
        if(isset($data["gerencia"]) && $data["gerencia"] != null) {
            
            $tmpsplit = preg_split("/-/", $data["gerencia"]);
            $code = null;
            $name = "";
            if(count($tmpsplit) > 1) {
                $code = trim($tmpsplit[0]);
                $name = trim($tmpsplit[1]);
            } else {
                $name = trim($data["gerencia"]);
            }
            
            $gerencia = $gControl->saveGerencia([
               "code" => $code,
               "name" => $name,
               "status" => "active"     
            ]);
        }
        
        $location = null;
        if(isset($data["ubicacion"]) && $data["ubicacion"] != null) {
            
            $params = [
                "sede" => [
                    "code" => null,
                    "name" => null
                ],
                "name" => trim($data["ubicacion"]["nombre"]),
                "description" => trim($data["ubicacion"]["referencia"]),
                "status" => "active"
            ];
            
            $tmpsplit = preg_split("/::/", $data["ubicacion"]["sede"]);
            if(count($tmpsplit) > 1) {
                $params["sede"]["code"] = trim($tmpsplit[0]);
                $params["sede"]["name"] = trim($tmpsplit[1]);
            } else {
                $params["sede"]["name"] = trim($data["ubicacion"]["sede"]);
            }
            
            $location = $lControl->saveLocation($params);
        }
        
        $equipos = [];
        if(isset($data["equipos"]) && count($data["equipos"]) > 0 ) {
            
            foreach ($data["equipos"] as $equipodata) {
                
                $params = [
                    "tipo" => strtoupper(trim($equipodata["tipo"])),
                    "marca" => strtoupper(trim($equipodata["marca"])),
                    "modelo" => strtoupper(trim($equipodata["modelo"])),
                    "comentarios" => trim($equipodata["comments"]),
                    "serial" => trim($equipodata["serial"]),
                    "estadoequipo" => "01",
                    "status" => "active"
                ];
                
                if(isset($equipodata["status"]) && $equipodata["status"] != "") {
                    $tmpsplit = preg_split("/-/", $equipodata["status"]);
                    if(count($tmpsplit) > 1) {
                        $params["estadoequipo"] = trim($tmpsplit[0]); 
                    } else {
                        $params["estadoequipo"] = trim($equipodata["status"]);
                    } 
                }
                
                if(isset($equipodata["nombrered"]) && $equipodata["nombrered"] != "") {
                    $params["nombrehost"] = $equipodata["nombrered"];
                }
                
                if(isset($equipodata["memoria"]) && $equipodata["memoria"] != "") {
                    $params["memoria"] = $equipodata["memoria"];
                }
                
                if(isset($equipodata["disco"]) && $equipodata["disco"] != "") {
                    $params["discoduro"] = $equipodata["disco"];
                }
                
                if(isset($equipodata["ext"]) && $equipodata["ext"] != "") {
                    $params["nombrehost"] = $equipodata["ext"];
                }
                
                if(isset($equipodata["so"]) && $equipodata["so"] != "") {
                    $params["sistema"] = $equipodata["so"];
                }
                
                if(isset($equipodata["num"]) && $equipodata["num"] != "") {
                    $params["memoria"] = $equipodata["num"];
                }
                
                if(isset($equipodata["procesador"]) && $equipodata["procesador"] != "") {
                    $params["procesador"] = $equipodata["procesador"];
                }
                
                if(isset($equipodata["apps"]) && is_array($equipodata["apps"])) {
                    $params["applications"] = $equipodata["apps"];
                }

                $equipo = $dControl->saveDevice($params);
                if($equipo->getId() > 0) {
                    array_push($equipos, $equipo);
                }
                
            }

        }
       
        $colaboradores = [];
        if(isset($data["colaboradores"]) && $data["colaboradores"] != null) {
            
            foreach ($data["colaboradores"] as $nameColaborador) {
                $colaborador = $cControl->saveColaborador([
                    "name" => $nameColaborador,
                    "status" => "active"     
                ]);
                
                array_push($colaboradores, $colaborador);
            }
        }
        
        $paramsLastMove = [
            "gerencia" => $gerencia,
            "ubicacion" =>  $location,
            "asignado" => $asignado,
            "equipos" => $equipos,
            "colaboradores" => $colaboradores 
        ];

        $dControl->saveLastMoveDevice($paramsLastMove);
        
        $arrayResponse = [
            "status" => $asignado->getEstatus(),
            "gerencia" => $gerencia->getEstatus(),
            "ubicacion" =>  $location->getEstatus(),
            "asignado" => $asignado->getEstatus(),
            "equipos" => count($equipos) > 0 ? "success" : "error",
            "message" => $asignado->getMessage()
        ];

        $this->response(json_encode($arrayResponse), 200);
    }
    
    public function autorizar($data) {
        $response = [
            "status" => "error",
            "message" => "missed attribute correo"
        ];

        if(!isset($data["correo"])) {
            $this->response(json_encode($response), 200);
            return;
        }
        
        if(trim($data["correo"]) == '') {
            $response["message"] = "el atributo correo no puede ser vacío";
            $this->response(json_encode($response), 200);
            return;
        }
        
        $availablelist = [
            "francisco.gomez@fvf.com.ve",
            "rafael.velasquez@fvf.com.ve",
            "wilman.sanchez@fvf.com.ve",
            "manuel.castillo@fvf.com.ve",
            "jorman.salazar@fvf.com.ve"
        ];
        
        if(array_search(trim($data["correo"]), $availablelist) === FALSE) {
            $response["message"] = "correo: " . $data["correo"] . " no se encuentra autorizado";
            $this->response(json_encode($response), 403);
            return;
        }
        
        $response["status"] = "success";
        $response["message"] = "user: " . $data["correo"];
         $response["user"] =  $data["correo"];
        
        $this->response(json_encode($response), 200);
    }
    
    public function hardware($data) {
        $response = [
            "status" => "error",
            "message" => "problem database connection"
        ];

	//only for autocomplete queries
	if(isset($data["filterempty"]) && trim($data["like"]) == "") {
		$this->response(json_encode($response), 200);
		return;
	}

        $deviceControl = new DeviceControl();
        $filter = [];
        
        if(isset($data["like"]) && $data["like"] != null) {
            $filter["like"] = $data["like"];
        }
        
        $ldata = $deviceControl->getALLDevicesPagination($filter, 1000, 1);
        if($ldata == null) {
            $this->response(json_encode($response), 200);
            return;
        }
        
        $response["status"] = "success";
        $response["message"] = "";
        $response["total"] = $ldata["total"];
        if($response["total"] > 0) {
            $response["data"] = [];
            $value = new Device();
            foreach ($ldata["data"] as $value) {
                array_push($response["data"], $value->getJSONobject());
            }
        } 
          
        
        $this->response(json_encode($response), 200);
    }
    
    public function employees($data) {
        $response = [
            "status" => "error",
            "message" => "problem database connection"
        ];
        
        
        $eControl = new ColaboradorControl();
        $filter = [];
        
        if(isset($data["like"]) && $data["like"] != null) {
            $filter["like"] = $data["like"];
        }
        
        $ldata = $eControl->getALLColaboradoresPagination($filter, 1000, 1);
        if($ldata == null) {
            $this->response(json_encode($response), 200);
            return;
        }
        
        $response["status"] = "success";
        $response["message"] = "";
        $response["total"] = $ldata["total"];
        if($response["total"] > 0) {
            $response["data"] = [];
            $value = new Colaborador();
            foreach ($ldata["data"] as $value) {
                array_push($response["data"], $value->getJSONobject());
            }
        }
        
        $this->response(json_encode($response), 200);
    }
    
    public function equipo($data) {
        $response = [
            "status" => "error",
            "message" => "problem database connection"
        ];
        
        $deviceControl = new DeviceControl();
        $filter = [];
        
        if(!isset($data["id"]) || $data["id"] == null) {
            $response["message"] = "Falta el parametro ID para devolver la consulta";
            $this->response(json_encode($response), 200);
            return;
        }
        
        $filter["id"] = [$data["id"]] ;

        $ldata = $deviceControl->getALLDevicesPagination($filter, 1, 1);
        if($ldata == null || $ldata["total"] <= 0) {
            $response["message"] = "Device Not Found";
            $this->response(json_encode($response), 200);
            return;
        }
        
        // $equipo = new Device();
        $equipo = $ldata["data"][0];
        
        $jsonDevice = $equipo->getJSONobject();
        $appsdevice = [];
        $appsControl = new ApplicationControl();
        
        $ladata = $appsControl->getALLApplications(["bydevice" => $equipo->getId()]);
        if($ladata != null && count($ladata) > 0){
            // $app = new Application();
            foreach ($ladata as $app) {
                array_push($appsdevice, $app->getDes());
            }
            $jsonDevice["apps"] = $appsdevice;
        }
        
        $response["status"] = "success";
        $response["message"] = "";
        $response["data"] = [
            "equipos" => [$jsonDevice]
        ];
        
        $lastmoves = $deviceControl->getLastMoveRowDataByDevice($equipo->getId());
        if($lastmoves == null || count($lastmoves) <=0) {
            
            $this->response(json_encode($response), 200);
            return;
        }
        
        
        $cControl = new ColaboradorControl();        
        $lasignado = $cControl->getALLColaboradores(["id" => $lastmoves[0]["responsable_id"]]);
        
        if($lasignado != null && count($lasignado) > 0) {
            $response["data"]["asignado"] = $lasignado[0]->getDes();
        }
        
        $gControl = new GerenciaControl();        
        $lgerencia = $gControl->getALLGerencias(["id" => $lastmoves[0]["gerencia_id"]]);
        if($lgerencia != null && count($lgerencia) > 0) {
            $response["data"]["gerencia"] = $lgerencia[0]->getCode() . " - ". $lgerencia[0]->getDes();
        }
        
        $lControl = new LocationControl();
        $llocation = $lControl->getALLLocations(["id" => $lastmoves[0]["localidad_id"]] );
        if($llocation != null && count($llocation) > 0) {
           
            $response["data"]["ubicacion"] = [
                "nombre" => $llocation[0]->getName(),
                "referencia" => $llocation[0]->getDes(),
                "id" => $llocation[0]->getId()
            ]; 
            
            $lSede = $lControl->getALLSedes(["id" => $llocation[0]->getIdSede() ]);
            if($lSede != null && count($lSede) > 0) {
                $response["data"]["ubicacion"]["sede"] = $lSede[0]->getDes();
            }
                    
        }
        
        $this->response(json_encode($response), 200);
    }
    
    public function delequipo($data) {
        
        $response = [
            "status" => "error",
            "message" => "Method not allowed for this function"
        ];
        
        if($this->get_request_method() != "DELETE") {
            $this->response(json_encode($response), 200);
            return;
        }

        $deviceControl = new DeviceControl();
        $filter = [];
        
        if(!isset($data["id"]) || $data["id"] == null) {
            $response["message"] = "Falta el parametro ID para eliminar el equipo";
            $this->response(json_encode($response), 200);
            return;
        }
        
        $filter["id"] = [$data["id"]] ;

        $ldata = $deviceControl->getALLDevicesPagination($filter, 1, 1);
        if($ldata == null || $ldata["total"] <= 0) {
            $response["message"] = "Device Not Found";
            $this->response(json_encode($response), 200);
            return;
        }
        
        // $equipo = new Device();
        $equipo = $ldata["data"][0];
        
        $tmpobj = $deviceControl->delDevice($equipo->getId());
        
        $response["status"] = $tmpobj->getEstatus();
        $response["message"] = $tmpobj->getMessage();
        $response["data"] = $equipo->getJSONobject();
        
        $this->response(json_encode($response), 200);
    }
  /*  
    public function saveSalidaEquipo($data) {
        
        $response = [
            "status" => "error",
            "message" => "Method not allowed for this function"
        ];
        
        if($this->get_request_method() != "POST") {
            $this->response(json_encode($response), 200);
            return;
        }

        
        
        
        $deviceControl = new DeviceControl();
        $filter = [];
        
        if(!isset($data["id"]) || $data["id"] == null) {
            $response["message"] = "Falta el parametro ID para eliminar el equipo";
            $this->response(json_encode($response), 200);
            return;
        }
        
        $filter["id"] = [$data["id"]] ;

        $ldata = $deviceControl->getALLDevicesPagination($filter, 1, 1);
        if($ldata == null || $ldata["total"] <= 0) {
            $response["message"] = "Device Not Found";
            $this->response(json_encode($response), 200);
            return;
        }
        
        // $equipo = new Device();
        $equipo = $ldata["data"][0];
        
        $tmpobj = $deviceControl->delDevice($equipo->getId());
        
        $response["status"] = $tmpobj->getEstatus();
        $response["message"] = $tmpobj->getMessage();
        $response["data"] = $equipo->getJSONobject();
        
        $this->response(json_encode($response), 200);
    }
*/
//    public function saveDataFromJApp($data) {
//
//        $show = 30;
//        $offset = 0;
//
//        if (isset($data["limit"]) && $data["limit"] > 0) {
//            $show = intval($data["limit"]);
//        }
//
//        if (isset($data["offset"]) && $data["offset"] > 0) {
//            $offset = intval($data["offset"]);
//        }
//
//        $arrayResponse = array(
//            "total" => 0,
//            "limit" => $show,
//            "offset" => $offset,
//            "data" => 0
//        );
//
//        $this->response(json_encode($arrayResponse), 200);
//    }

}

// Initiiate Library

$api = new Api();
$api->processApi();
