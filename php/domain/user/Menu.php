<?php

class Menu extends Combo {
    
    private $url;
    private $visible;
    private $padre;
    private $cssChild;
    private $javascript;
    private $order;
    
    function getVisible() {
        return $this->visible;
    }

    function setVisible($visible) {
        $this->visible = $visible;
    }

    function getUrl() {
        return $this->url;
    }

    function getPadre() {
        return $this->padre;
    }

    function getCssChild() {
        return $this->cssChild;
    }

    function getJavascript() {
        return $this->javascript;
    }

    function getOrder() {
        return $this->order;
    }

    function setUrl($url) {
        $this->url = $url;
    }

    function setPadre($padre) {
        $this->padre = $padre;
    }

    function setCssChild($cssChild) {
        $this->cssChild = $cssChild;
    }

    function setJavascript($javascript) {
        $this->javascript = $javascript;
    }

    function setOrder($order) {
        $this->order = $order;
    }
            
    function getJSONobject() {

        $obj = [
            "id" => $this->getId(),
            "descripcion" => $this->getDes(),            
            "status" => $this->getEstatus(),
            "url" => $this->getUrl(),
            "padre" => $this->getPadre(),
            "csschild" => $this->getCssChild(),
            "javascript" => $this->getJavascript(),
            "order" => $this->getOrder()
        ];
        
        return $obj;
    }
}
?>
