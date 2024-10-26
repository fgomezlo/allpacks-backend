<?php

class Rol extends Combo {
    
    function getJSONobject() {

        $obj = [
            "id" => $this->getId(),
            "name" => $this->getDes(),            
            "status" => $this->getEstatus()
        ];
        
        return $obj;
    }
}
?>
