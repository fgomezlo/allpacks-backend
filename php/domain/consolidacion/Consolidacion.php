<?php

class Consolidacion extends Combo {

    private $idcliente;
    private $nota;
    private $idusuario;
    private $observacion;
    private $tipoServicio;
    
    private $lstItems;
    
    function addItem($item) {
        if($this->lstItems == null) {
            $this->lstItems = [];
        }
        
        $this->lstItems[] = $item;
    }
    
    function getLstItems() {
        return $this->lstItems;
    }

    function setLstItems($lstItems) {
        $this->lstItems = $lstItems;
    }    
    
    function getIdcliente() {
        return $this->idcliente;
    }

    function getNota() {
        return $this->nota;
    }

    function getIdusuario() {
        return $this->idusuario;
    }

    function getObservacion() {
        return $this->observacion;
    }

    function getTipoServicio() {
        return $this->tipoServicio;
    }

    function setIdcliente($idcliente) {
        $this->idcliente = $idcliente;
    }

    function setNota($nota) {
        $this->nota = $nota;
    }

    function setIdusuario($idusuario) {
        $this->idusuario = $idusuario;
    }

    function setObservacion($observacion) {
        $this->observacion = $observacion;
    }

    function setTipoServicio($tipoServicio) {
        $this->tipoServicio = $tipoServicio;
    }
    
    public function getCodConsolidacion() {
        $tmp = date('Y');
        if($this->getDateCreated() != null) {
            //error_log("fecha: " . $this->fecha);
            /* $fecha = DateTime::createFromFormat('d/m/Y H:i:s', $this->fecha);
            $tmp = $fecha->format('Y'); */
            
            $arrayfecha = preg_split("/[\s]+/",$this->getDateCreated());
            $fecha = $arrayfecha[0];
            $arrayelemento = preg_split("/\//",$fecha);
            $tmp = $arrayelemento[2];
            
        }
        
        return sprintf("%d / %2$05d", $tmp, $this->getId());
    }
    
    function getJSONobject() {

        $obj = [
            "id" => $this->getId(),            
            "nota" => $this->getNota(),
            "status" => $this->getEstatus(),
            "dateupdated" => $this->getDateUpdated(),
            "observacion" => $this->getObservacion(),
            "tiposervicio" => $this->getTipoServicio(),
            "codigo" => $this->getCodConsolidacion(),
            "dateupdated" => $this->getDateUpdated(),
            "datecreated" => $this->getDateUpdated()
        ];
        
        if($this->getIdcliente() != null) {
            
            if($this->getIdcliente() instanceof Customer) {
                 $obj["cliente"] = $this->getIdcliente()->getJSONobject();
            } else {
                $obj["cliente"] = [
                    "id" => $this->getIdcliente()
                ];
            }
        } 
        
        if($this->getIdusuario() != null) {
            
            if($this->getIdusuario() instanceof User) {
                 $obj["usuario"] = $this->getIdusuario()->getJSONobject();
            } else {
                $obj["usuario"] = [
                    "id" => $this->getIdusuario()
                ];
            }

        } 
        
        if($this->lstItems == null || count($this->lstItems) == 0)
            return $obj;
        
        // array roles 
        $items = [];
        foreach ($this->lstItems as $item) {
            $items[] = $item->getJSONobject();
        }
        $obj["items"] = $items;
        
        return $obj;
    }
}
?>
