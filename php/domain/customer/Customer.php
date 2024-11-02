<?php

class Customer extends Combo {

//    private $id;
    private $empresa;
//    private $nombre;
    private $direccion;
    private $ciudad;
    private $estado;
    private $pais;
    private $destino;
    private $telefono;
    private $email;
    private $notas;
//    private $activo;
    private $password;
    private $codigo;
    private $idCodigoCliente;
    private $sync;
    
    function getEmpresa() {
        return $this->empresa;
    }

    function getDireccion() {
        return $this->direccion;
    }

    function getCiudad() {
        return $this->ciudad;
    }

    function getEstado() {
        return $this->estado;
    }

    function getPais() {
        return $this->pais;
    }

    function getDestino() {
        return $this->destino;
    }

    function getTelefono() {
        return $this->telefono;
    }

    function getEmail() {
        return $this->email;
    }

    function getNotas() {
        return $this->notas;
    }

    function getPassword() {
        return $this->password;
    }

    function getCodigo() {
        return $this->codigo;
    }

    function getIdCodigoCliente() {
        return $this->idCodigoCliente;
    }

    function getSync() {
        return $this->sync;
    }

    function setEmpresa($empresa) {
        $this->empresa = $empresa;
    }

    function setDireccion($direccion) {
        $this->direccion = $direccion;
    }

    function setCiudad($ciudad) {
        $this->ciudad = $ciudad;
    }

    function setEstado($estado) {
        $this->estado = $estado;
    }

    function setPais($pais) {
        $this->pais = $pais;
    }

    function setDestino($destino) {
        $this->destino = $destino;
    }

    function setTelefono($telefono) {
        $this->telefono = $telefono;
    }

    function setEmail($email) {
        $this->email = $email;
    }

    function setNotas($notas) {
        $this->notas = $notas;
    }

    function setPassword($password) {
        $this->password = $password;
    }

    function setCodigo($codigo) {
        $this->codigo = $codigo;
    }

    function setIdCodigoCliente($idCodigoCliente) {
        $this->idCodigoCliente = $idCodigoCliente;
    }

    function setSync($sync) {
        $this->sync = $sync;
    }
        
    function getJSONobject() {

        $obj = [
            "id" => $this->getId(),
            "name" => $this->getDes(),            
            "status" => $this->getEstatus(),
            "email" => $this->getEmail(),
            "empresa" => $this->getEmpresa(),
            "direccion" => $this->getDireccion(),
            "ciudad" => $this->getCiudad(),
            "estado" => $this->getEstado(),
            "pais" => $this->getPais(),
            "destino" => $this->getDestino(),
            "telefono" => $this->getTelefono(),
            "notas" => $this->getNotas(),
            "password" => $this->getPassword(),
            "codigo" => $this->getCodigo(),
            "sync" => $this->getSync(),
            "datecreated" => $this->getDateCreated()
        ];
        
        return $obj;
    }
}
?>
