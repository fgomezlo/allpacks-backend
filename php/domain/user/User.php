<?php

class User extends Combo {

    private $email;
    private $password;
    private $dni;
    private $token;
    
    private $lstRoles;
    
    function addRol($rol) {
        if($this->lstRoles == null) {
            $this->lstRoles = [];
        }
        
        $this->lstRoles[] = $rol;
    }
    
    function getLstRoles() {
        return $this->lstRoles;
    }
    
    function setLstRoles($lstRoles) {
        $this->lstRoles = $lstRoles;
    }

    function getEmail() {
        return $this->email;
    }

    function getPassword() {
        return $this->password;
    }

    function getDni() {
        return $this->dni;
    }

    function getToken() {
        return $this->token;
    }

    function setEmail($email) {
        $this->email = $email;
    }

    function setPassword($password) {
        $this->password = $password;
    }

    function setDni($dni) {
        $this->dni = $dni;
    }

    function setToken($token) {
        $this->token = $token;
    }

        
    function getJSONobject() {

        $obj = [
            "id" => $this->getId(),
            "name" => $this->getDes(),            
            "status" => $this->getEstatus(),
            "email" => $this->getEmail(),
            "dni" => $this->getDni(),
            "roles" => []
        ];
        
        if($this->lstRoles == null || count($this->lstRoles) == 0)
            return $obj;
        
        // array roles 
        $roles = [];
        foreach ($this->lstRoles as $rol) {
            $roles[] = $rol->getJSONobject();
        }
        $obj["roles"] = $roles;
        
        return $obj;
    }
}
?>
