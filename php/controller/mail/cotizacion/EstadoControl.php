<?php

class EstadoControl {
    
    /** @var EstadoDAOImpl */
    private $EstadoDAOImpl;

    public function  __construct() {
        $this->EstadoDAOImpl = new EstadoDAOImpl();
    }
    
    public function getALLEstado() {
        return $this->EstadoDAOImpl->getALLEstado();
    }
    
    public function getALLEstadoConsolidar() {
        return $this->EstadoDAOImpl->getALLEstadoConsolidar();
    }
    
    public function getALLEstadoByParams($codEstatus) {
        return $this->EstadoDAOImpl->getALLEstadoConsolidar($codEstatus);
    }
    
    public function getEstadoConsolidarById($id) {
        return $this->EstadoDAOImpl->getEstadoConsolidarById($id);
    }
    
    public function getEstadoById($id) {
        return $this->EstadoDAOImpl->getEstadoById($id);
    }
    public function getEstadoIdByName($name){
        
        return $this->EstadoDAOImpl->getEstadoIdByName($name);
    }
    
}
?>
