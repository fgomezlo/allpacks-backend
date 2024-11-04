<?php

class ConsolidacionItem extends Combo {

    private $carrier;
    private $tracking;
    private $idconsolidacion;
    private $warehouse;
    private $nota;
    private $valor;
    private $urlfactura;
    
    function getCarrier() {
        return $this->carrier;
    }

    function getTracking() {
        return $this->tracking;
    }

    function getIdconsolidacion() {
        return $this->idconsolidacion;
    }

    function getWarehouse() {
        return $this->warehouse;
    }

    function getNota() {
        return $this->nota;
    }

    function getValor() {
        return $this->valor;
    }

    function getUrlfactura() {
        return $this->urlfactura;
    }

    function setCarrier($carrier) {
        $this->carrier = $carrier;
    }

    function setTracking($tracking) {
        $this->tracking = $tracking;
    }

    function setIdconsolidacion($idconsolidacion) {
        $this->idconsolidacion = $idconsolidacion;
    }

    function setWarehouse($warehouse) {
        $this->warehouse = $warehouse;
    }

    function setNota($nota) {
        $this->nota = $nota;
    }

    function setValor($valor) {
        $this->valor = $valor;
    }

    function setUrlfactura($urlfactura) {
        $this->urlfactura = $urlfactura;
    }
    
    function getJSONobject() {

        $obj = [
            "id" => $this->getId(),
            "carrier" => $this->getCarrier(),            
            "tracking" => $this->getTracking(),            
            "descripcion" => $this->getDes(),
            "status" => $this->getEstatus(),
            "warehouse" => $this->getWarehouse(),
            "nota" => $this->getNota(),
            "valor" => $this->getValor(),
            "urlfactura" => $this->getUrlfactura()
        ];
        
        return $obj;
    }
}
?>
