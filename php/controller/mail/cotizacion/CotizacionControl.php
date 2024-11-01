<?php

class CotizacionControl {

    /** @var CotizacionDAOImpl */
    private $cotizacionDAO;

    function __construct() {

        $this->cotizacionDAO = new CotizacionDAOImpl();
    }

    /**
     * Funcion que busca si el cotizacion esta dentro de la base de datos por id
     * @param string $id identificador en la base de datos del cotizacion a buscar
     * @return Cotizacion el cual contiene la informacion completa del cotizacion, en caso contrario devuelve NULL
     */
    public function buscarCotizacionById($id) {
        $consulta = $this->cotizacionDAO->buscarCotizacionById($id);

        //TODO: aqui podemos agregar el extra

        return $consulta;
    }

    public function getCotizacionByClientID($id) {
        $consulta = $this->cotizacionDAO->getCotizacionByClientID($id);

        //TODO: aqui podemos agregar el extra

        return $consulta;
    }

    public function getItemsByCotizacion($cotizacionId) {
        return $this->cotizacionDAO->getItemsCotizacionByIdCotizacion($cotizacionId);
    }

    public function buscarCotizacionByConsigneeCode($consignee) {

        $consulta = $this->cotizacionDAO->buscarCotizacionByConsigneeCode($consignee);

        //TODO: aqui podemos agregar el extra

        return $consulta;
    }

    public function autenticaCotizacion($nombreUsuario, $clave) {

        $cotizacion = $this->cotizacionDAO->buscarCotizacionByConsigneeCode($nombreUsuario);
        // verificamos que haya conseguido el usuario
        if ($cotizacion != null) {

            // verificamos si el usuario no ha sido dado de baja
            if ($cotizacion->getActivo() != 1) {
                return null;
            }

            //verificamos pass del usuario
            if ($cotizacion->getPassword() != $clave) {
                return null;
            }
        }

        return $cotizacion;
    }

    public function getAllCotizacion($idcotizacion) {
        $consulta = $this->asegurarDAO->getAllCotizacion($idcotizacion);

        //TODO: aqui podemos agregar el extra

        return $consulta;
    }

    /**
     * Funcion que trae a todos los cotizacions del sistema, por filtros
     * @param <type> $correo
     * @param <type> $nombres
     * @param <type> $tipoCotizacion cuando es igual a -1, significan todos los codigos
     * @param <type> $telefono
     * @param <type> $activo cuando es igual a -1, significan todos los codigos
     * @param <type> $logCotizacion
     * @return <type>
     */
    public function consultaGeneralCotizacion($nombre, $email, $estado, $fechaDesde, $fechaHasta) {

        if ($estado == -1)
            $estado = null;

        $consulta = $this->cotizacionDAO->getCotizacionByFilter($nombre, $email, $estado, $fechaDesde, $fechaHasta);

        return $consulta;
    }

    /**
     * Funcion para guardar los datos de la cotizacion solicitada por el cliente
     * @param string $nombre
     * @param string $email
     * @param string $ciudad
     * @param int $destino
     * @param array $link arreglo de tipo string con el contenido de los links de los productos
     * @param array $itemdesc arreglo de tipo string con el contenido de la descripcion de los productos
     * @param array $cantidad arreglo de tipo float con el contenido de la cantidad de productos a comprar
     * @param array $width arreglo de tipo float con el contenido del ancho de los productos a comprar
     * @param array $height arreglo de tipo float con el contenido del alto de los productos a comprar
     * @param array $depth arreglo de tipo float con el contenido del profundidad de los productos a comprar
     * @param array $weight arreglo de tipo float con el contenido del peso de los productos a comprar
     * @param array $tipoEnvio arreglo de tipo int con el contenido del tipo de envio (1 aerepo 2 maritimo)
     * @param array $seguro arreglo de tipo int con el contenido sobre si va asegurar los productos a comprar
     * @param array $valor arreglo de tipo float con el contenido del valor de los productos a comprar
     * @param int $estado
     * @param string $message
     * @return type
     */
    public function guardarCotizacion($nombre, $email, $ciudad, $destino, $link, $itemdesc, $cantidad, $width, $height, $depth, $weight, $tipoEnvio, $seguro, $valor, $estado, $tipocotizacion, $telefono, &$message, $cliente = null) {

        // verificamos si el log de cotizacion existe
        // $cotizacion = null;
        // if ($cotizacion == null) {

        $cotizacion = new Cotizacion();
        // cargamos los datos agregados por el cotizacion en el objeto
        $cotizacion->setNombrefull($nombre);
        $cotizacion->setEmail($email);
        $cotizacion->setDestino($destino);
        $cotizacion->setCiudad($ciudad);
        $cotizacion->setEstado($estado);
        $cotizacion->setTipoCotizacion($tipocotizacion);
        $cotizacion->setTelefono($telefono);
        $cotizacion->setId_cliente($cliente != null ? $cliente->getId() : null);

        // variables del mail
        $items = array();
        $correoCotizacion = $cotizacion->getEmail();
        if ($this->validarFormularioCotizacion($itemdesc)) {
            // cargamos los datos del cotizacion en la base de datos
            $cotizacion = $this->cotizacionDAO->ingresarCotizacion($cotizacion);
            if ($cotizacion->getId() != null) {

                for ($i = 0; $i < count($itemdesc); $i++) {

                    $item = new Item();
                    $item->setItemlink(trim($link[$i]) != "" ? $link[$i] : null);
                    $item->setItemdescripcion(trim($itemdesc[$i]) != "" ? $itemdesc[$i] : null);
                    $item->setPiezas(trim($cantidad[$i]) != "" ? $cantidad[$i] : null);
                    $item->setWidth(trim($width[$i]) != "" ? $width[$i] : null);
                    $item->setHeight(trim($height[$i]) != "" ? $height[$i] : null);
                    $item->setDepth(trim($depth[$i]) != "" ? $depth[$i] : null);
                    $item->setWeight(trim($weight[$i]) != "" ? $weight[$i] : null);
                    $item->setTipodeenvio($tipoEnvio[$i]);
                    $item->setSeguro($seguro[$i]);
                    $item->setValor(trim($valor[$i]) != "" ? $valor[$i] : null);
                    $item->setIdcotizacion($cotizacion->getId());

                    array_push($items, $item);

                    $this->cotizacionDAO->ingresarItemCotizacion($item);
                }

                MailControl::sendCotizacionByMailNuevo($cotizacion, $cliente, $items, $correoCotizacion);
                $message = CotizacionMessages::getRegistroCotizacionExitoMessage($cotizacion->getCodCotizacion());
            } else {
                $message = CotizacionMessages::getErrorInsertarMessage();
            }
        } else {
          $message = "No existen artículos cargados, porfavor recuerde darle clic al botón agregar artículo antes de enviar la solicitud de cotización";
          } 

        return $cotizacion;
    }

    private function validarFormularioCotizacion($items) {
        
        if(count($items) < 1)
            return false;
        
        return true;
    }

    public function guardarCotizacionCliente($id_cliente, $ciudad, $destino, $link, $itemdesc, $cantidad, $width, $height, $depth, $weight, $tipoEnvio, $seguro, $valor, $estado, &$message) {

        // verificamos si el log de cotizacion existe
        $cotizacion = null;

        if ($cotizacion == null) {

            $cotizacion = new Cotizacion();
            // cargamos los datos agregados por el cotizacion en el objeto
            $cotizacion->setId_cliente($id_cliente);
            $cotizacion->setDestino($destino);
            $cotizacion->setCiudad($ciudad);
            $cotizacion->setItemlink($link);
            $cotizacion->setItemdescripcion($itemdesc);
            $cotizacion->setPiezas($cantidad);
            $cotizacion->setWidth($width);
            $cotizacion->setHeight($height);
            $cotizacion->setDepth($depth);
            $cotizacion->setWeight($weight);
            $cotizacion->setTipodeenvio($tipoEnvio);
            $cotizacion->setSeguro($seguro);
            $cotizacion->setValor($valor);
            $cotizacion->setEstado($estado);


            // cargamos los datos del cotizacion en la base de datos
            $cotizacion = $this->cotizacionDAO->ingresarCotizacionCliente($cotizacion);

            if ($cotizacion->getIdcotizacion() != null) {

                $message = CotizacionMessages::getRegistroCotizacionExitoMessage($cotizacion->getNombrefull());
            } else
                $message = CotizacionMessages::getErrorInsertarMessage();
        } else {
            $message = CotizacionMessages::getExisteCotizacionMessage();
        }

        return $cotizacion;
    }

    /**
     * 
     * @param Cotizacion $precargado
     * @param type $nombre
     * @param type $email
     * @param type $destino
     * @param type $ciudad
     * @param type $link
     * @param type $itemdesc
     * @param type $cantidad
     * @param type $width
     * @param type $height
     * @param type $depth
     * @param type $weight
     * @param type $tipoEnvio
     * @param type $seguro
     * @param type $valor
     * @param type $estado
     * @param type $userlog
     * @param type $message
     * @return type 
     */
    public function actualizarCotizacion($precargado, $link, $itemdesc, $cantidad, $width, $height, $depth, $weight, $tipoEnvio, $seguro, $valor, $userlog, $presupuesto, $observacion, $valorSeguro, $valorElectronico, $valorTipoEnvio, $tarifaaerea, $tarifamaritima, $observacioncompra, &$message) {

        $cotizacion = $precargado;
        if ($cotizacion != NULL) {

            $cotizacion->setEstado(2);
            $cotizacion->setUsuario($userlog);
            $cotizacion->setObservacion($observacion);
            $cotizacion->setTarifaAerea($tarifaaerea);
            $cotizacion->setTarifaMaritima($tarifamaritima);
            $cotizacion->setObservacionCompra($observacioncompra);

            // variables para el mail
            $totalCosto = 0;
            $items = array();
            $correoCotizacion = $cotizacion->getEmail();
            // cargamos los datos del cotizacion en la base de datos
            //echo "verificando" . $cotizacion->getId();
            $cotizacionHist = $this->cotizacionDAO->actualizaCotizacion($cotizacion);
            if ($cotizacionHist > 0) {

                $this->cotizacionDAO->deleteCotizacionItems($cotizacionHist, $cotizacion);

                for ($i = 0; $i < count($itemdesc); $i++) {

                    $item = new Item();
                    $item->setItemlink($link[$i]);
                    $item->setItemdescripcion($itemdesc[$i]);
                    $item->setPiezas($cantidad[$i]);
                    $item->setWidth($width[$i]);
                    $item->setHeight($height[$i]);
                    $item->setDepth($depth[$i]);
                    $item->setWeight($weight[$i]);
                    $item->setTipodeenvio($tipoEnvio[$i]);
                    $item->setSeguro($seguro[$i]);
                    $item->setValor($valor[$i]);
                    $item->setIdcotizacion($cotizacion->getId());
                    $item->setValorPresupuesto($presupuesto[$i]);
                    $item->setSeguroValue($valorSeguro[$i]);
                    $item->setSeguroElectronico($valorElectronico[$i]);
                    $item->setValorTipoEnvio($valorTipoEnvio[$i]);

                    array_push($items, $item);
                    $totalCosto += $presupuesto[$i];

                    $this->cotizacionDAO->ingresarItemCotizacion($item);
                }

                // TODO: enviar correo con la informacion de la cotizacion
                $cliente = null;
                if ($cotizacion->getId_cliente() != null) {
                    $clienteControl = new ClienteControl();
                    $cliente = $clienteControl->buscarClienteById($cotizacion->getId_cliente());
                    $correoCotizacion = $cliente->getEmail();
                }

                MailControl::sendCotizacionByMail($cotizacion, $cliente, $items, $totalCosto, $correoCotizacion);
                $message = CotizacionMessages::getCotizacionActualizadoMessage($cotizacion->getCodCotizacion());
            } else {
                $message = CotizacionMessages::getErrorInsertarMessage();
            }


            return $cotizacion;
        }

        return null;
    }

    /**
     * Funcion que elimina el cotizacion del sistema si este no ha realizado ninguna operacion, en caso contrario lo inhabilita
     * @param int $idCotizacion
     */
    public function borrarCotizacion($idCotizacion, $observaciones, $idusuario, &$message) {

        $existe = $this->buscarCotizacionById($idCotizacion);
        if ($existe != null) {

            $existe->setEstado(4);
            $existe->setObservacion($observaciones);
            $existe->setUsuario($idusuario);

            $this->cotizacionDAO->actualizaCotizacion($existe);
            $message = CotizacionMessages::getEliminarMessage($existe->getCodCotizacion());

            return 1;
        }

        $message = CotizacionMessages::getNoExisteCotizacionMessage();
        return 0;
    }

    public function getRolesCotizacion($idUser) {

        $rolDAO = new RolDAOImpl();
        return $rolDAO->getRolByCotizacion($idUser);
    }

    /**
     * 
     * @param Cotizacion $cotizacion 
     */
    public function getRolesDescripcionCotizacion($cotizacion) {
        $perfil = "";

        $perfiles = $this->getRolesCotizacion($cotizacion->getId());
        for ($j = 0; $j < count($perfiles); $j++) {
            $rol = $perfiles[$j];
            $perfil .= ($j != 0 ? ", " : "") . $rol->getDes();
        }

        return $perfil;
    }

    public function sincronizarCotizacionDelDia($fechainicio, $fechafin) {

        $ws = new WebServiceControl();
        $registros = $ws->connectAllPacks($fechainicio, $fechafin);
        $paiscontrol = new PaisControl();
        $destinocontrol = new DestinoControl();

        echo count($registros) . "<br />";
        // TODO: recorrido de las filas que te trajo la consulta
        for ($i = 0; $i < count($registros); $i++) {
            $valor = $registros[$i];
            $cotizacions = new CotizacionXml();
            $cotizacions->setConsigneeCode($valor['ConsigneeCode']);
            $cotizacions->setName($valor['Name']);
            $cotizacions->setContact($valor['Contact']);
            $cotizacions->setAdd1($valor['Add1']);
            $cotizacions->setAdd2($valor['Add2']);
            $cotizacions->setCity($valor['City']);
            $cotizacions->setState(isset($valor['State']) && $valor['State'] != null ? $valor['State'] : null );
            $cotizacions->setCountry($valor['Country']);
            $cotizacions->setEmail($valor['Email']);
            $cotizacions->setDestination($valor['Destination']);
            $cotizacions->setPassword(isset($valor['Password']) ? $valor['Password'] : null);
            $cotizacions->setID1(isset($valor['ID1']) ? $valor['ID1'] : null);
            $cotizacions->setID2(isset($valor['ID2']) ? $valor['ID2'] : null);
            error_log("entre");
            $this->cotizacionDAO->ingresarCotizacionXMLSinFormato($cotizacions);
            error_log("sali");
            $listapais = $paiscontrol->getPaisIdByName(substr($valor['Country'], 0, -1));
            $listadestino = $destinocontrol->getDestinoIdByName(substr($valor['Destination'], 0, -1));

            $encontrado = $this->cotizacionDAO->buscarCotizacionByConsigneeCode($valor['ConsigneeCode']);

            if ($encontrado == null) {
                echo "Pais " . $valor['Country'] . " Destino " . $valor['Destination'] . "<br  />";

                //if (($listapais != NULL) && ($listadestino != NULL)) {
                echo "Pais Insert" . $valor['Country'] . " Destino Insert" . $valor['Destination'] . "<br  />";
                $cotizacions->setCountry($listapais != NULL ? $listapais->getId() : 5);
                $cotizacions->setDestination($listadestino != null ? $listadestino->getId() : 6);

                $this->cotizacionDAO->ingresarCotizacionXML($cotizacions);

                //}
                // TODO: verificar si inserto o actualizo en la base de datos
            } else {
                // acutLIZAMOS EL REGISTRO EN BASE DATOS CLIENTE
                $encontrado->setPassword($cotizacions->getPassword());
                $encontrado->setEmail($cotizacions->getEmail());
                $encontrado->setDireccion($cotizacions->getAdd1() . " " . $cotizacions->getAdd2());
                $encontrado->setNombre($cotizacions->getContact());
                $encontrado->setEmpresa($cotizacions->getName());
                if (($listapais != NULL) && ($listadestino != NULL)) {
                    $encontrado->setPais($listapais->getId());
                    $encontrado->setDestino($listadestino->getId());
                }

                $this->cotizacionDAO->actualizaCotizacion($encontrado);
            }
        }

        echo "<br />termineeeeeeeeeeeeeee";
    }

    public function sincronizarRecibos($fechainicio, $fechafin) {

        $ws = new WebServiceControl();
        $registros = $ws->connectAllPacksReceip($fechainicio, $fechafin);


        echo count($registros) . "<br />";
        // TODO: recorrido de las filas que te trajo la consulta
        for ($i = 0; $i < count($registros); $i++) {
            $valor = $registros[$i];
            $recibos = new ReceiptXml();
            $recibos->setDate($valor['Date']);
            $recibos->setReceipt($valor['Receipt']);
            $recibos->setShipperID($valor['ShipperID']);
            $recibos->setShipper($valor['Shipper']);
            $recibos->setConsigneeID($valor['ConsigneeID']);
            $recibos->setConsignee($valor['Consignee']);
            $recibos->setAgentID($valor['AgentID']);
            $recibos->setAgent($valor['Agent']);
            $recibos->setPieces($valor['Pieces']);
            $recibos->setWeight($valor['Weight']);
            $recibos->setVolume($valor['Volume']);
            $recibos->setWeightVol($valor['WeightVol']);
            $recibos->setItemDescription($valor['ItemDescription']);
            $recibos->setNotes($valor['Notes']);
            $recibos->setCountryID($valor['CountryID']);

            $this->cotizacionDAO->ingresarReceiptsXML($recibos);

            // TODO: verificar si inserto o actualizo en la base de datos
        }


        echo "<br />termineeeeeeeeeeeeeee";
    }

}

?>
