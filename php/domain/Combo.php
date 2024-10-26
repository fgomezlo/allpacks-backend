<?php

/**
 * Description of Gerencia
 * 
 * @author franciscogomez
 */
class Combo {
    //put your code here
     
    private $id;
    private $des;
    private $message;
    private $estatus;
    private $ejson;
    private $dateCreated;
    private $dateUpdated;
    
    function getId() {
        return $this->id;
    }

    function getDes() {
        return $this->des;
    }

    function getMessage() {
        return $this->message;
    }

    function getEstatus() {
        return $this->estatus;
    }

    function getEjson() {
        return $this->ejson;
    }

    function getDateCreated() {
        return $this->dateCreated;
    }

    function getDateUpdated() {
        return $this->dateUpdated;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setDes($des) {
        $this->des = $des;
    }

    function setMessage($message) {
        $this->message = $message;
    }

    function setEstatus($estatus) {
        $this->estatus = $estatus;
    }

    function setEjson($ejson) {
        $this->ejson = $ejson;
    }

    function setDateCreated($dateCreated) {
        $this->dateCreated = $dateCreated;
    }

    function setDateUpdated($dateUpdated) {
        $this->dateUpdated = $dateUpdated;
    }
    
    function getJSONobject() {
        return [
            "id" => $this->id,
            "des" => $this->des,
            "message" => $this->message,
            "estatus" => $this->estatus
        ];
    }
    
}

?>
