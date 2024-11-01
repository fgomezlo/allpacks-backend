<?php

class MailControl {

    private static $noreplay = "No Reply Allpacksfc <no-replay@allpacksfc.com>";
    private static $noreplaytest = "no-replay@allpacksfc.com";
    private static $nopass = "Franpoto2014$";
    private static $noreplayname = "No-Reply Allpacksfc";
    private static $testing = false;
    private static $SMTP = "smtp.1and1.com";
    private static $SMTP_PORT = 587;

    /**
     * Funcion que envia el mail de la cotizacion elaborada por los funcionarios de allpacks
     * @param Cotizacion $cotizacion
     * @param Cliente $cliente
     * @param array $items
     * @param string $totalCosto
     * @param string $correoCotizacion
     * @return type
     */
    public static function sendCotizacionByMail($cotizacion, $cliente, $items, $totalCosto, $correoCotizacion) {
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
        $headers .= 'From: ' . MailControl::$noreplay . "\r\n";

        $bodyMail = "<html>
<body style='font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px'>

<p style=\"text-align:left; \"><img src=\"" . $GLOBALS['config']['baseproy'] . "images/All_packs_Logo.jpg\" alt=\"asegura\" height=\"129\" /></p>
<p style=\"margin-top:-10px; font-size: 10px; text-align:left; \">All Packs Freight Cargo, Inc.<br/>
6162 NW 74th Ave<br/>
Miami, Florida 33166<br/>
Tel (305) 592-4814</p>

<table style=\"width: 100%\">
                    <tr>
                        <td><p style=\"font-size: 16px\">Datos del Cliente</p></td>
                        <td><p style=\"font-size: 16px\">Datos de la cotización</p></td>
                    </tr>
                    <tr>
                        <td>
                            <table style=\"width: 100%\">
                                <tr>
                                    <td>Codigo cliente:</td>
                                    <td>" . ($cliente != null ? $cliente->getCodigo() : "NA") . "</td>
                                </tr>
                                <tr>
                                    <td>Nombre:</td>
                                    <td>" . ($cliente != null ? $cliente->getNombre() : $cotizacion->getNombrefull()) . "</td>
                                </tr>
                                <tr>
                                    <td>Direccion:</td>
                                    <td>" . ($cliente != null ? $cliente->getDireccion() : $cotizacion->getCiudad() ) . "</td>
                                </tr>
                                <tr>
                                    <td>Teléfono:</td>
                                    <td>" . ($cliente != null ? $cliente->getTelefono() : $cotizacion->getTelefono() ) . "</td>
                                </tr>
                            </table>
                        </td>
                        <td style=\"vertical-align: top\">
                            <table style=\"width: 100%\">
                                <tr>
                                    <td>Numero cotización</td>
                                    <td>" . $cotizacion->getCodCotizacion() . "</td>
                                </tr>
                                <tr>
                                    <td>Tipo de cotización</td>
                                    <td>" . ($cotizacion->getTipoCotizacion() == 1 ? "Sólo envío" : "Compra y envío") . "</td>
                                </tr>
                                <tr>
                                    <td>Fecha cotización</td>
                                    <td>" . date('d/m/Y') . "</td>
                                </tr>
                                <tr>
                                    <td>Total items cotización</td>
                                    <td><span style=\"font-size: 20px; font-weight: bold\" >" . ($items != null ? count($items) : 0) . "</span></td>
                                </tr>
                                <tr>
                                    <td>Costo estimado</td>
                                    <td><span style=\"font-size: 20px; font-weight: bold\" > " . $totalCosto . " $ USD </span></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan=\"2\">Observaciones de la cotización</td>
                    </tr>
                    <tr>
                        <td colspan=\"2\">" . $cotizacion->getObservacion() . "</td>
                    </tr>
                </table>
                <p>Items de la cotizacion</p>
                <table style=\"width: 100%; border: 1px solid rgb(61,83,122); border-radius: 5px\">
                    <thead>
                    <tr>
                            <th rowspan=\"2\" style=\"font-size:14px; text-align:center; font-weight: bold; color:#FFFFFF; 
                            background-color:#3d537a;border-width: 2px; border-right-style: solid;border-right-color: #330000;
                            border-bottom-style: solid; border-bottom-color: #330000;\">#</th>
                            <th rowspan=\"2\" style=\"font-size:14px; text-align:center; font-weight: bold; color:#FFFFFF; 
                            background-color:#3d537a;border-width: 2px; border-right-style: solid;border-right-color: #330000;
                            border-bottom-style: solid; border-bottom-color: #330000;\">Link</th>
                            <th rowspan=\"2\" style=\"font-size:14px; text-align:center; font-weight: bold; color:#FFFFFF; 
                            background-color:#3d537a;border-width: 2px; border-right-style: solid;border-right-color: #330000;
                            border-bottom-style: solid; border-bottom-color: #330000;\">Descripción</th>
                            <th rowspan=\"2\" style=\"font-size:14px; text-align:center; font-weight: bold; color:#FFFFFF; 
                            background-color:#3d537a;border-width: 2px; border-right-style: solid;border-right-color: #330000;
                            border-bottom-style: solid; border-bottom-color: #330000;\">Can.</th>
                            <th rowspan=\"2\" style=\"font-size:14px; text-align:center; font-weight: bold; color:#FFFFFF; 
                            background-color:#3d537a;border-width: 2px; border-right-style: solid;border-right-color: #330000;
                            border-bottom-style: solid; border-bottom-color: #330000;\">An</th> 
                            <th rowspan=\"2\" style=\"font-size:14px; text-align:center; font-weight: bold; color:#FFFFFF; 
                            background-color:#3d537a;border-width: 2px; border-right-style: solid;border-right-color: #330000;
                            border-bottom-style: solid; border-bottom-color: #330000;\">Al</th> 
                            <th rowspan=\"2\" style=\"font-size:14px; text-align:center; font-weight: bold; color:#FFFFFF; 
                            background-color:#3d537a;border-width: 2px; border-right-style: solid;border-right-color: #330000;
                            border-bottom-style: solid; border-bottom-color: #330000;\">Pr</th>
                            <th rowspan=\"2\" style=\"font-size:14px; text-align:center; font-weight: bold; color:#FFFFFF; 
                            background-color:#3d537a;border-width: 2px; border-right-style: solid;border-right-color: #330000;
                            border-bottom-style: solid; border-bottom-color: #330000;\">Peso Vol</th>
                            <th rowspan=\"2\" style=\"font-size:14px; text-align:center; font-weight: bold; color:#FFFFFF; 
                            background-color:#3d537a;border-width: 2px; border-right-style: solid;border-right-color: #330000;
                            border-bottom-style: solid; border-bottom-color: #330000;\">Peso </th>
                            <th rowspan=\"2\" style=\"font-size:14px; text-align:center; font-weight: bold; color:#FFFFFF; 
                            background-color:#3d537a;border-width: 2px; border-right-style: solid;border-right-color: #330000;
                            border-bottom-style: solid; border-bottom-color: #330000;\">Pc <sup>3</sup> </th>
                            <th rowspan=\"2\" style=\"font-size:14px; text-align:center; font-weight: bold; color:#FFFFFF; 
                            background-color:#3d537a;border-width: 2px; border-right-style: solid;border-right-color: #330000;
                            border-bottom-style: solid; border-bottom-color: #330000;\">Valor ($) </th>
                            <th rowspan=\"2\" style=\"font-size:14px; text-align:center; font-weight: bold; color:#FFFFFF; 
                            background-color:#3d537a;border-width: 2px; border-right-style: solid;border-right-color: #330000;
                            border-bottom-style: solid; border-bottom-color: #330000;\">Costo de Envío</th>
                            <th colspan=\"2\" style=\"font-size:14px; text-align:center; font-weight: bold; color:#FFFFFF; 
                            background-color:#3d537a;border-width: 2px; border-right-style: solid;border-right-color: #330000;
                            border-bottom-style: solid; border-bottom-color: #330000;\"><img src=\"" . $GLOBALS['config']['baseproy'] . "images/asegura_btn.png\" alt=\"asegura\" /></th>
                            <th rowspan=\"2\" style=\"font-size:14px; text-align:center; font-weight: bold; color:#FFFFFF; 
                            background-color:#3d537a;border-width: 2px; border-right-style: solid;border-right-color: #330000;
                            border-bottom-style: solid; border-bottom-color: #330000;\">Elec</th>
                            <th rowspan=\"2\" style=\"font-size:14px; text-align:center; font-weight: bold; color:#FFFFFF; 
                            background-color:#3d537a;border-width: 2px; border-right-style: solid;border-right-color: #330000;
                            border-bottom-style: solid; border-bottom-color: #330000;\">Total $ USD</th>
                        </tr>
                        <tr>
                            <th style=\"font-size:14px; text-align:center; font-weight: bold; color:#FFFFFF; 
                            background-color:#3d537a;border-width: 2px; border-right-style: solid;border-right-color: #330000;
                            border-bottom-style: solid; border-bottom-color: #330000;\" >AA</th>
                            <th style=\"font-size:14px; text-align:center; font-weight: bold; color:#FFFFFF; 
                            background-color:#3d537a;border-width: 2px; border-right-style: solid;border-right-color: #330000;
                            border-bottom-style: solid; border-bottom-color: #330000;\" >DF</th>
                        </tr>
                    </thead>
                    <tbody>";


        for ($i = 0; $i < count($items); $i++) {
            $item = $items[$i];
            $tarifa = $cotizacion->getTarifaAerea();
            if (!($item->getTipodeenvio() == 1)) {
                $tarifa = $cotizacion->getTarifaMaritima();
            }
            $bodyMail .= "
                            <tr style=\"" . ($i % 2 == 0 ? "background-color:rgb(236,236,236)" : "") . "; text-align: right\">
                                <td>
                                    <span>" . ($i + 1) . "</span>
                                </td>
                                <td>
                                    <a href=\"" . ($item->getItemlink()) . "\" target=\"_blank\">Ver</a>
                                </td>
                                <td>
                                    <p title=\"" . $item->getDescripcionForTitle() . "\">" . $item->getDescripcionForVista() . "</p>
                                </td>
                                <td>
                                    <p>" . $item->getPiezas() . "</p>
                                </td>
                                <td>
                                    <p>" . $item->getWidth() . "</p>
                                </td>
                                <td>
                                    <p>" . $item->getHeight() . "</p>
                                </td>
                                <td>
                                    <p>" . $item->getDepth() . "</p>
                                </td>
                                <td>
                                    <p>" . $item->getPesoVolumen() . "</p>
                                </td>
                                <td>
                                    <p>" . $item->getWeight() . "</p>
                                </td>
                                <td>
                                    <p>" . $item->getPesoCubico() . "</p>
                                </td>
                                <td>
                                    <p >" . $item->getValor() . "</p>
                                </td>
                                <td>
                                    <table style=\"width:100%\">
                                        <tr>
                                            <td style=\"width:10px\">" . ($item->getTipodeenvio() == 1 ? "<img src=\"" . $GLOBALS['config']['baseproy'] . "images/airConsolidated.gif\" />" : "<img src=\"" . $GLOBALS['config']['baseproy'] . "images/BoatConsolidated.gif\" />") . "</td>
                                            <td style=\"text-align: right\">" . $item->getCostoEnvio($tarifa) . "</td>
                                        </tr>
                                    </table>
                                </td>
                                <td>
                                    <p >" . $item->getCostoSeguroAA() . "</p>
                                </td>
                                <td>
                                    <p >" . $item->getCostoSeguroDF() . "</p>
                                </td>
                                <td>
                                    <p >" . $item->getSeguroElectronico() . "</p>
                                </td>
                                <td style=\"font-weight: bold; \">
                                    " . $item->getValorPresupuesto($tarifa) . "
                                </td>
                            </tr> ";
        }
        $bodyMail .= "</tbody>
                </table>";

        if ($cotizacion->getObservacionCompra() != null) {
            $bodyMail .= "<p><b>Nota:</b> " . $cotizacion->getObservacionCompra() . " </p>";
        }

        $bodyMail .= "<p >Esta cotización está siendo realizada bajo las descripciones que usted nos proporciona, una vez la carga sea recibida por nosotros en nuestro almacén en Miami, recibirá un correo electrónico con una nota de almacén en la que podrá observar las medidas finales del producto. Esta cotización no tendrá validez si las medidas o peso son distintas a las antes descritos por usted o su  proveedor.</p>
<p>Por experiencia le hacemos saber que la información (medidas y peso) que aparece en la pagina web de los proveedores en pocas oportunidades son las correctas, nosotros estaremos dispuestos a realizarle la cotización con la información suministrada sin embargo, si usted desea una cotización más exacta puede escribirle al proveedor solicitándole las medidas y peso de su articulo en su empaque original. Es importante tener en cuenta que el costo del envio está reflejado en base a las medidas y peso reales de la caja.</p>
<p>Esta cotización tiene como validez 7 días a partir de la emisión.</p>
</body>
</html>";
        // 
        if (!MailControl::$testing)
            return mail($correoCotizacion, " Allpacksfc.com --- Cotización No. " . $cotizacion->getCodCotizacion(), $bodyMail, $headers);

        error_log("mail: " . $bodyMail);
        return false;
    }

    public static function sendCotizacionByMailNuevo($cotizacion, $cliente, $items, $correoCotizacion) {
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
        $headers .= 'From: ' . MailControl::$noreplay . "\r\n";

        $bodyMail = "<html>
<body style='font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px'>

<p style=\"text-align:left; \"><img src=\"" . $GLOBALS['config']['baseproy'] . "images/All_packs_Logo.jpg\" alt=\"asegura\" height=\"129\" /></p>
<p style=\"margin-top:-10px; font-size: 10px; text-align:left; \">All Packs Freight Cargo, Inc.<br/>
6162 NW 74th Ave<br/>
Miami, Florida 33166<br/>
Tel (305) 592-4814</p>

<table style=\"width: 100%\">
                    <tr>
                        <td><p style=\"font-size: 16px\">Datos del Cliente</p></td>
                        <td><p style=\"font-size: 16px\">Datos de la cotización</p></td>
                    </tr>
                    <tr>
                        <td>
                            <table style=\"width: 100%\">
                                <tr>
                                    <td>Codigo cliente:</td>
                                    <td>" . ($cliente != null ? $cliente->getCodigo() : "NA") . "</td>
                                </tr>
                                <tr>
                                    <td>Nombre:</td>
                                    <td>" . ($cliente != null ? $cliente->getNombre() : $cotizacion->getNombrefull()) . "</td>
                                </tr>
                                <tr>
                                    <td>Direccion:</td>
                                    <td>" . ($cliente != null ? $cliente->getDireccion() : $cotizacion->getCiudad() ) . "</td>
                                </tr>
                                <tr>
                                    <td>Teléfono:</td>
                                    <td>" . ($cliente != null ? $cliente->getTelefono() : $cotizacion->getTelefono() ) . "</td>
                                </tr>
                            </table>
                        </td>
                        <td style=\"vertical-align: top\">
                            <table style=\"width: 100%\">
                                <tr>
                                    <td>Numero cotización</td>
                                    <td>" . $cotizacion->getCodCotizacion() . "</td>
                                </tr>
                                <tr>
                                    <td>Tipo de cotización</td>
                                    <td>" . ($cotizacion->getTipoCotizacion() == 1 ? "Sólo envío" : "Compra y envío") . "</td>
                                </tr>
                                <tr>
                                    <td>Fecha cotización</td>
                                    <td>" . date('d/m/Y') . "</td>
                                </tr>
                                <tr>
                                    <td>Total items cotización</td>
                                    <td><span style=\"font-size: 20px; font-weight: bold\" >" . ($items != null ? count($items) : 0) . "</span></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <p>Items de la cotizacion</p>
                <table style=\"width: 100%; border: 1px solid rgb(61,83,122); border-radius: 5px\">
                    <thead>
                    <tr>
                            <th  style=\"font-size:14px; text-align:center; font-weight: bold; color:#FFFFFF; 
                            background-color:#3d537a;border-width: 2px; border-right-style: solid;border-right-color: #330000;
                            border-bottom-style: solid; border-bottom-color: #330000;\">#</th>
                            <th  style=\"font-size:14px; text-align:center; font-weight: bold; color:#FFFFFF; 
                            background-color:#3d537a;border-width: 2px; border-right-style: solid;border-right-color: #330000;
                            border-bottom-style: solid; border-bottom-color: #330000;\">Link</th>
                            <th  style=\"font-size:14px; text-align:center; font-weight: bold; color:#FFFFFF; 
                            background-color:#3d537a;border-width: 2px; border-right-style: solid;border-right-color: #330000;
                            border-bottom-style: solid; border-bottom-color: #330000;\">Descripción</th>
                            <th  style=\"font-size:14px; text-align:center; font-weight: bold; color:#FFFFFF; 
                            background-color:#3d537a;border-width: 2px; border-right-style: solid;border-right-color: #330000;
                            border-bottom-style: solid; border-bottom-color: #330000;\">Can.</th>
                            <th  style=\"font-size:14px; text-align:center; font-weight: bold; color:#FFFFFF; 
                            background-color:#3d537a;border-width: 2px; border-right-style: solid;border-right-color: #330000;
                            border-bottom-style: solid; border-bottom-color: #330000;\">An</th> 
                            <th  style=\"font-size:14px; text-align:center; font-weight: bold; color:#FFFFFF; 
                            background-color:#3d537a;border-width: 2px; border-right-style: solid;border-right-color: #330000;
                            border-bottom-style: solid; border-bottom-color: #330000;\">Al</th> 
                            <th  style=\"font-size:14px; text-align:center; font-weight: bold; color:#FFFFFF; 
                            background-color:#3d537a;border-width: 2px; border-right-style: solid;border-right-color: #330000;
                            border-bottom-style: solid; border-bottom-color: #330000;\">Pr</th>
                            <th  style=\"font-size:14px; text-align:center; font-weight: bold; color:#FFFFFF; 
                            background-color:#3d537a;border-width: 2px; border-right-style: solid;border-right-color: #330000;
                            border-bottom-style: solid; border-bottom-color: #330000;\">Peso Vol</th>
                            <th  style=\"font-size:14px; text-align:center; font-weight: bold; color:#FFFFFF; 
                            background-color:#3d537a;border-width: 2px; border-right-style: solid;border-right-color: #330000;
                            border-bottom-style: solid; border-bottom-color: #330000;\">Peso </th>
                            <th  style=\"font-size:14px; text-align:center; font-weight: bold; color:#FFFFFF; 
                            background-color:#3d537a;border-width: 2px; border-right-style: solid;border-right-color: #330000;
                            border-bottom-style: solid; border-bottom-color: #330000;\">Pc <sup>3</sup> </th>
                            <th  style=\"font-size:14px; text-align:center; font-weight: bold; color:#FFFFFF; 
                            background-color:#3d537a;border-width: 2px; border-right-style: solid;border-right-color: #330000;
                            border-bottom-style: solid; border-bottom-color: #330000;\">Valor ($) </th>
                            <th  style=\"font-size:14px; text-align:center; font-weight: bold; color:#FFFFFF; 
                            background-color:#3d537a;border-width: 2px; border-right-style: solid;border-right-color: #330000;
                            border-bottom-style: solid; border-bottom-color: #330000;\">Tipo de Envío</th>
                            <th style=\"font-size:14px; text-align:center; font-weight: bold; color:#FFFFFF; 
                            background-color:#3d537a;border-width: 2px; border-right-style: solid;border-right-color: #330000;
                            border-bottom-style: solid; border-bottom-color: #330000;\"><img src=\"" . $GLOBALS['config']['baseproy'] . "images/asegura_btn.png\" alt=\"asegura\" /></th>
                        </tr>
                    </thead>
                    <tbody>";


        for ($i = 0; $i < count($items); $i++) {
            $item = $items[$i];
            $tarifa = $cotizacion->getTarifaAerea();
            if (!($item->getTipodeenvio() == 1)) {
                $tarifa = $cotizacion->getTarifaMaritima();
            }
            $bodyMail .= "
                            <tr style=\"" . ($i % 2 == 0 ? "background-color:rgb(236,236,236)" : "") . "; text-align: right\">
                                <td>
                                    <span>" . ($i + 1) . "</span>
                                </td>
                                <td>
                                    <a href=\"" . ($item->getItemlink()) . "\" target=\"_blank\">Ver</a>
                                </td>
                                <td>
                                    <p title=\"" . $item->getDescripcionForTitle() . "\">" . $item->getDescripcionForVista() . "</p>
                                </td>
                                <td>
                                    <p>" . $item->getPiezas() . "</p>
                                </td>
                                <td>
                                    <p>" . $item->getWidth() . "</p>
                                </td>
                                <td>
                                    <p>" . $item->getHeight() . "</p>
                                </td>
                                <td>
                                    <p>" . $item->getDepth() . "</p>
                                </td>
                                <td>
                                    <p>" . $item->getPesoVolumen() . "</p>
                                </td>
                                <td>
                                    <p>" . $item->getWeight() . "</p>
                                </td>
                                <td>
                                    <p>" . $item->getPesoCubico() . "</p>
                                </td>
                                <td>
                                    <p >" . $item->getValor() . "</p>
                                </td>
                                <td>
                                    <table style=\"width:100%\">
                                        <tr>
                                            <td style=\"width:10px\">" . ($item->getTipodeenvio() == 1 ? "<img src=\"" . $GLOBALS['config']['baseproy'] . "images/airConsolidated.gif\" />" : "<img src=\"" . $GLOBALS['config']['baseproy'] . "images/BoatConsolidated.gif\" />") . "</td>
                                        </tr>
                                    </table>
                                </td>
                                <td>
                                    <p > " . ($item->getSeguro() == 1 ? "<img src=\"" . $GLOBALS['config']['baseproy'] . "images/check.png\" />" : "" ) . " </p>
                                </td>
                                
                            </tr> ";
        }
        $bodyMail .= "</tbody>
                </table>";

        $bodyMail .= "<p >Esta cotización ha sido solicitada por usted y ha sido registrada en nuestro sistema, en pocos momentos estaremos respondiendo su cotización. 
            En caso que no haya realizado esta operación porfavor envíenos un correo a info@allpacksfc.com informando el error.</p>
</body>
</html>";

        if (!MailControl::$testing)
            return mail($correoCotizacion, " Allpacksfc.com --- Registro de Cotización No. " . $cotizacion->getCodCotizacion(), $bodyMail, $headers);

        error_log("mail: " . $bodyMail);
        return false;
    }

    /**
     * Funcion que envia el mail del contacto publico elaborado por el cliente
     * @param type $nombre
     * @param type $mail
     * @param type $pais
     * @param type $mensaje
     * @return type
     */
    public static function sendContactoPublicByMail($nombre, $mail, $pais, $ciudad, $mensaje) {
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
        $headers .= 'From: ' . MailControl::$noreplay . "\r\n";

        $bodyMail = "<html>
<body style='font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px'>
<p style=\"text-align:left; \"><img src=\"" . $GLOBALS['config']['baseproy'] . "images/All_packs_Logo.jpg\" alt=\"asegura\" height=\"129\" /></p>
<p style=\"margin-top:-10px; font-size: 10px; text-align:left; \">All Packs Freight Cargo, Inc.<br/>
6162 NW 74th Ave<br/>
Miami, Florida 33166<br/>
Tel (305) 592-4814</p>

<table style=\"width: 100%\">
       <tbody>
                    <tr>
                        <td><p style=\"font-size: 16px\">Nombre cliente: </p></td>
                        <td>" . $nombre . "</td>
                    </tr>
                    <tr>
                        <td><p style=\"font-size: 16px\">Mail cliente: </p></td>
                        <td>" . $mail . "</td>
                    </tr>
                    <tr>
                        <td><p style=\"font-size: 16px\">Pais: </p></td>
                        <td>" . $pais . "</td>
                    </tr>
                    <tr>
                        <td><p style=\"font-size: 16px\">Ciudad: </p></td>
                        <td>" . $ciudad . "</td>
                    </tr>
                    <tr>
                        <td colspan=\"2\"><p style=\"font-size: 16px\">Mensaje: </p></td>
                    </tr>
                    <tr>
                        <td colspan=\"2\">" . $mensaje . "</td>
                    </tr>
                </tbody>
                </table>
</body>
</html>";

        if (!MailControl::$testing)
            return mail("info@allpacksfc.com", " Allpacksfc.com - " . $nombre . " - solicitud información contactenos", $bodyMail, $headers);

        error_log("mail: " . $bodyMail);
        return false;
    }

    public static function sendMailAsegurarCarga($consigneecode, $contenido, $shownotas, $remitente) {
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
        $headers .= 'From: ' . MailControl::$noreplay . "\r\n";

        $bodyMail = "<html>
            <body style='font-family:Verdana, Arial, Helvetica, sans-serif'>
            <p style=\"text-align:left; \"><img src=\"" . $GLOBALS['config']['baseproy'] . "images/All_packs_Logo.jpg\" alt=\"asegura\" height=\"129\" /></p>
<p style=\"margin-top:-10px; font-size: 10px; text-align:left; \">All Packs Freight Cargo, Inc.<br/>
6162 NW 74th Ave<br/>
Miami, Florida 33166<br/>
Tel (305) 592-4814</p>
                $contenido ";

        if ($shownotas)
            $bodyMail .= "<p><b>Importante:</b> En caso de no haber realizado esta operación por favor enviar un correo electronico a info@allpacksfc cancelando esta operación, en un lapso no mayor a 24 horas</p>";

        $bodyMail .= "<div style=\"width: 100%;\">
                    <p style=\"text-align: justify; font-size: 12px\"><b>Nota: </b>Para optimizar y agilizar la Solicitud de Reempaque o Consolidación de la carga, usted podrá generar Solicitudes con un máximo de 7 Tracking Numbers, 
                        esta norma está creada para evitar errores y tiempo de espera. Le recordamos que usted podrá generar diferentes ordenes, las mismas no podrán ser modificadas una vez enviadas, es decir quedaran cerradas de tal manera que usted no podrá incluir o eliminar artículos. 
                        Este Servicio es totalmente Gratis.
                        <b>All Packs Freight Cargo Inc</b> no se hace responsable de artículos faltantes al momento de reempacar, usted será notificado a través de un correo electrónico de la descripción y contenido de la mercancía que recibimos a su nombre, usted debe informar de algún faltante o discrepancia al momento de recibir los correos informativos.</p>
                </div>";

        $bodyMail .= "</body>
        </html>";

        //echo "<div id=\"joder\">" . $bodyMail . "</div>";
        if (!MailControl::$testing)
            return mail($remitente, "Allpacksfc.com --- Solicitud de reempaque o consolidación del cliente $consigneecode", $bodyMail, $headers);

        error_log("mail: " . $bodyMail);
        return false;
    }

    public static function sendPaypalMail($consigneecode, $consigneenombre, $warehouse, $correoconsignee, $monto) {
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
        $headers .= 'From: ' . MailControl::$noreplay . "\r\n";


        $bodyMail = "<html>
            <body style='font-family:Verdana, Arial, Helvetica, sans-serif'>
            <p style=\"text-align:left; \"><img src=\"" . $GLOBALS['config']['baseproy'] . "images/All_packs_Logo.jpg\" alt=\"asegura\" height=\"129\" /></p>
<p style=\"margin-top:-10px; font-size: 10px; text-align:left; \">All Packs Freight Cargo, Inc.<br/>
6162 NW 74th Ave<br/>
Miami, Florida 33166<br/>
Tel (305) 592-4814</p>";

        $bodyMail .= "<div style=\"width: 100%;\">
                    <p style=\"text-align: justify; font-size: 12px\">
                        El Cliente $consigneenombre ($consigneecode) ha solicitado realizar el pago del wh ($warehouse) con un monto estimado según sistema de $monto a través de paypal.
                    </p>
                    <p style=\"text-align: justify; font-size: 12px\">
                        El correo suministrado por el cliente para el envío de la solicitud de pago es <span style\"font-size: 14px\">$correoconsignee</span>
                    </p>
                </div>";

        $bodyCliente = $bodyMail;
        $bodyCliente .= "<div style=\"width: 100%;\">
                    <p style=\"text-align: justify; font-size: 12px\">
                        Nota: este correo fue enviado de manera automática, si usted no realizó esta operación por favor contactarnos inmediatemente por el correo info@allpacksfc.com para informar
                        el error ocasionado por el sistema. Gracias.
                    </p>
                </div>";

        $bodyMail .= "</body>
        </html>";

        //echo "<div id=\"joder\">" . $bodyMail . "</div>";
        if (!MailControl::$testing) {
            mail($correoconsignee, "Allpacksfc.com --- Solicitud de pago paypal cliente $consigneecode y warehouse $warehouse", $bodyCliente, $headers);
            return mail("info@allpacksfc.com", "Allpacksfc.com --- Solicitud de pago paypal cliente $consigneecode y warehouse $warehouse", $bodyMail, $headers);
        }

        //error_log("mail: " . $bodyMail);
        return false;
    }

    /*
      public static function sendPaypalMailAutorizacion($idAutorizacion, $correoOcodigo, $tablewarehouse, $datos, $email, $codigoCliente, $total, $pathFact) {
      $headers = 'MIME-Version: 1.0' . "\r\n";
      $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
      $headers .= 'From: ' . MailControl::$noreplay . "\r\n";


      $bodyMail = "<html>
      <body style='font-family:Verdana, Arial, Helvetica, sans-serif'>
      <p style=\"text-align:left; \"><img src=\"" . $GLOBALS['config']['baseproy'] . "images/All_packs_Logo.jpg\" alt=\"asegura\" height=\"129\" /></p>
      <p style=\"margin-top:-10px; font-size: 10px; text-align:left; \">All Packs Freight Cargo, Inc.<br/>
      6162 NW 74th Ave<br/>
      Miami, Florida 33166<br/>
      Tel (305) 592-4814</p>";

      $bodyMail .= "<div style=\"width: 100%;\">
      <p style=\"text-align: justify; font-size: 12px\">
      El Cliente $datos ($codigoCliente) ha solicitado realizar el pago del wh ($tablewarehouse) y de la autorizacion ($idAutorizacion) con un monto estimado según sistema de $total a travez de paypal.
      </p>
      <p style=\"text-align: justify; font-size: 12px\">
      El correo suministrado por el cliente para el envío de la solicitud de pago es <span style\"font-size: 14px\">$correoOcodigo</span>
      </p>";
      $bodyMail .= $pathFact != null ? " <p>Descargar facturas: <a href = '" . $GLOBALS['config']['baseproy'] . $GLOBALS['config']['pathfactview'] . "/" . $pathFact . "'> aqui</a></p>" : "";
      $bodyMail .="  </div>";

      $bodyCliente = $bodyMail;
      $bodyCliente .= "<div style=\"width: 100%;\">
      <p style=\"text-align: justify; font-size: 12px\">
      Nota: este correo fue enviado de manera automática, si usted no realizó esta operación por favor contactarnos inmediatemente por el correo info@allpacksfc.com para informar
      el error ocasionado por el sistema. Gracias.
      </p>
      </div>";

      $bodyMail .= "</body>
      </html>";

      //echo "<div id=\"joder\">" . $bodyMail . "</div>";
      if (!MailControl::$testing) {
      mail($email, "Allpacksfc.com --- Solicitud de pago paypal cliente $codigoCliente y warehouse $tablewarehouse", $bodyCliente, $headers);
      return mail("info@allpacksfc.com", "Allpacksfc.com --- Solicitud de pago paypal cliente $codigoCliente y warehouse $tablewarehouse", $bodyMail, $headers);
      }

      error_log("mail: " . $bodyMail);
      return false;
      }

     */

    public static function sendGiftMail($consigneecode, $consigneenombre, $correoconsignee, $warehouse, $codgift, $monto) {
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
        $headers .= 'From: ' . MailControl::$noreplay . "\r\n";

        $bodyMail = "<html>
            <body style='font-family:Verdana, Arial, Helvetica, sans-serif'>
            <p style=\"text-align:left; \"><img src=\"" . $GLOBALS['config']['baseproy'] . "images/All_packs_Logo.jpg\" alt=\"asegura\" height=\"129\" /></p>
<p style=\"margin-top:-10px; font-size: 10px; text-align:left; \">All Packs Freight Cargo, Inc.<br/>
6162 NW 74th Ave<br/>
Miami, Florida 33166<br/>
Tel (305) 592-4814</p>";

        $bodyMail .= "<div style=\"width: 100%;\">
                    <p style=\"text-align: justify; font-size: 12px\">
                        El Cliente $consigneenombre ($consigneecode) ha solicitado realizar el pago del wh ($warehouse) con un monto estimado según sistema de $monto a través de giftcards.
                    </p>
                    <p style=\"text-align: justify; font-size: 12px\">
                        El codigo de la giftcard suministrado por el cliente es: <span style\"font-size: 14px\">$codgift</span>
                    </p>
                </div>";

        $bodyCliente = $bodyMail;
        $bodyCliente .= "<div style=\"width: 100%;\">
                    <p style=\"text-align: justify; font-size: 12px\">
                        Nota: este correo fue enviado de manera automática, si usted no realizó esta operación por favor contactarnos inmediatemente por el correo info@allpacksfc.com para informar
                        el error ocasionado por el sistema. Gracias.
                    </p>
                </div>";

        $bodyMail .= "</body>
        </html>";

        //echo "<div id=\"joder\">" . $bodyMail . "</div>";
        if (!MailControl::$testing) {
            mail($correoconsignee, "Allpacksfc.com --- Solicitud de pago giftcard cliente $consigneecode y warehouse ", $bodyCliente, $headers);
            return mail("info@allpacksfc.com", "Allpacksfc.com --- Solicitud de pago giftcard cliente $consigneecode y warehouse ", $bodyMail, $headers);
        }

        error_log("mail: " . $bodyMail);
        return false;
    }

    /* public static function sendGiftMailAutorizacion($idAutorizacion, $correoOcodigo, $tablewarehouse, $datos, $email, $codigoCliente, $total, $pathFact) {

      $headers = 'MIME-Version: 1.0' . "\r\n";
      $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
      $headers .= 'From: ' . MailControl::$noreplay . "\r\n";

      $bodyMail = "<html>
      <body style='font-family:Verdana, Arial, Helvetica, sans-serif'>
      <p style=\"text-align:left; \"><img src=\"" . $GLOBALS['config']['baseproy'] . "images/All_packs_Logo.jpg\" alt=\"asegura\" height=\"129\" /></p>
      <p style=\"margin-top:-10px; font-size: 10px; text-align:left; \">All Packs Freight Cargo, Inc.<br/>
      6162 NW 74th Ave<br/>
      Miami, Florida 33166<br/>
      Tel (305) 592-4814</p>";

      $bodyMail .= "<div style=\"width: 100%;\">
      <p style=\"text-align: justify; font-size: 12px\">
      El Cliente $datos ($codigoCliente) ha solicitado realizar el pago del wh ($tablewarehouse)  y de la autorizacion ($idAutorizacion) con un monto estimado según sistema de $total a travez de giftcards.
      </p>
      <p style=\"text-align: justify; font-size: 12px\">
      El codigo de la giftcard suministrado por el cliente es: <span style\"font-size: 14px\">$correoOcodigo</span>
      </p>";
      $bodyMail .= $pathFact != null ? " <p>Descargar facturas: <a href = '" . $GLOBALS['config']['baseproy'] . $GLOBALS['config']['pathfactview'] . "/" . $pathFact . "'> aqui</a></p>" : "";
      $bodyMail .="  </div>";


      $bodyCliente = $bodyMail;
      $bodyCliente .= "<div style=\"width: 100%;\">
      <p style=\"text-align: justify; font-size: 12px\">
      Nota: este correo fue enviado de manera automática, si usted no realizó esta operación por favor contactarnos inmediatemente por el correo info@allpacksfc.com para informar
      el error ocasionado por el sistema. Gracias.
      </p>
      </div>";

      $bodyMail .= "</body>
      </html>";

      //echo "<div id=\"joder\">" . $bodyMail . "</div>";
      if (!MailControl::$testing) {
      mail($email, "Allpacksfc.com --- Solicitud de pago giftcard cliente $codigoCliente y warehouse ", $bodyCliente, $headers);
      return mail("info@allpacksfc.com", "Allpacksfc.com --- Solicitud de pago giftcard cliente $codigoCliente y warehouse $tablewarehouse ", $bodyMail, $headers);
      }

      error_log("mail: " . $bodyMail);
      return false;
      }
     *
     */

    public static function sendMailConsolidaCarga($idServicio, $carriers, $tracking, $descripciones, $notas, $cliente, $shownotas, $valordeclarado, $otrodestino = null) {

        $contenido = "<p><b>Servicio Marítimo</b></p>";
        $notasvarias = "<p><b>Nota:</b> Usted a generado una solicitud de consolidación de carga para el servicio Marítimo, el personal de Allpacks Almacén esperará la llegada de la carga descrita
            para agruparlos en un solo recibo de almacén y así evitarle el cobro de mínimo de carga individuales</p>";
        if ($idServicio == 1) {
            $contenido = "<p><b>Servicio Aéreo de reempaque</b></p>";
            $notasvarias = "<p><b>Nota:</b> Usted a generado una solicitud de reempaque para el servicio Aéreo, el personal de Allpacks Almacén esperará la llegada de la carga descrita y 
            reempacará la misma con la finalidad de eliminar el volúmen (Espacio Vacío) de la carga en la medida que la mercancía lo permita. 
            Esta solicitud no podrá ser reenviada añadiendo o eliminando tracking numbers</p>";
        }

        $contenidoItems = "<table >
                    <tr>
                        <th style=\"text-align:left\">Item</th>
                        <th style=\"text-align:left\">Carrier</th>
                        <th style=\"text-align:left\">Tracking</th>
                        <th style=\"text-align:left\">Descripción</th>
                        <th style=\"text-align:left\">Valor Declarado</th>
                    </tr>";


        for ($i = 1; $i < count($carriers); $i++) {
            $contenidoItems .= "<tr>";
            $contenidoItems .= "<td style=\"border: 1px solid black;\">" . ($i) . "</td>";
            $contenidoItems .= "<td style=\"border: 1px solid black;\">" . $carriers[$i] . "</td>";
            $contenidoItems .= "<td style=\"border: 1px solid black;\">" . $tracking[$i] . "</td>";
            $contenidoItems .= "<td style=\"border: 1px solid black;\">" . $descripciones[$i] . "</td>";
            $contenidoItems .= "<td style=\"border: 1px solid black;\">" . $valordeclarado[$i] . "</td>";
            $contenidoItems .= "</tr>";
        }

        $contenidoItems .= "</table>";
        $contenido .= $contenidoItems;

        if ($notas != "") {
            $contenido .= "<p><b>Observaciones cliente:</b> $notas</p>";
        }

        $contenido .= $notasvarias;
        // llenamos la informacion del cliente
        $contenido = "<p>El cliente <b>" . $cliente->getNombre() . "(" . $cliente->getCodigo() . ")" . "</b> ha generado la siguiente solicitud: </p>" . $contenido;

        $bodyMail = "<html>
            <body style='font-family:Verdana, Arial, Helvetica, sans-serif'>
            <p style=\"text-align:left; \"><img src=\"" . $GLOBALS['config']['baseproy'] . "images/All_packs_Logo.jpg\" alt=\"asegura\" height=\"129\" /></p>
<p style=\"margin-top:-10px; font-size: 10px; text-align:left; \">All Packs Freight Cargo, Inc.<br/>
6162 NW 74th Ave<br/>
Miami, Florida 33166<br/>
Tel (305) 592-4814</p>
                $contenido ";

        if ($shownotas) {
            $bodyMail .= "<p><b>Importante:</b> En caso de no haber realizado esta operación por favor enviar un correo electronico a info@allpacksfc cancelando esta operación, en un lapso no mayor a 24 horas</p>";
        }

        $bodyMail .= "<div style=\"width: 100%;\">
                    <p style=\"text-align: justify; font-size: 12px\"><b>Nota: </b>Para optimizar y agilizar la Solicitud de Reempaque o Consolidación de la carga, usted podrá generar Solicitudes con un máximo de 7 Tracking Numbers, 
                        esta norma está creada para evitar errores y tiempo de espera. Le recordamos que usted podrá generar diferentes ordenes, las mismas no podrán ser modificadas una vez enviadas, es decir quedaran cerradas de tal manera que usted no podrá incluir o eliminar artículos. 
                        Este Servicio es totalmente Gratis.
                        <b>All Packs Freight Cargo Inc</b> no se hace responsable de artículos faltantes al momento de reempacar, usted sera notificado a través de un correo electrónico de la descripción y contenido de la mercancía que recibimos a su nombre, usted debe informar de algún faltante o discrepancia al momento de recibir los correos informativos.</p>
                </div>";

        $bodyMail .= "</body>
        </html>";

        //echo "<div id=\"joder\">" . $bodyMail . "</div>";
        if (!MailControl::$testing) {

            return MailControl::sentMail(
                            $otrodestino == null ? $cliente->getEmail() : $otrodestino, "Allpacksfc.com --- Solicitud de reempaque o consolidación del cliente " . $cliente->getIdCodigoCliente(), $bodyMail
            );
        }

        error_log("mail: " . $bodyMail);
        return false;
    }

    public static function sendAutorizacion($autorizacion, $mailCliente, $checkWare) {
        $lsitawarehouse = "";
        for ($m = 0; $m < count($checkWare); $m++) {
            $coma = "";
            if ($m + 1 < count($checkWare))
                $coma = ",";
            $lsitawarehouse.= $checkWare[$m] . $coma;
        }


        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
        $headers .= 'From: ' . MailControl::$noreplay . "\r\n";

        $bodyMail = "<html>
<body style='font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px'>

<p style=\"text-align:left; \"><img src=\"" . $GLOBALS['config']['baseproy'] . "images/All_packs_Logo.jpg\" alt=\"asegura\" height=\"129\" /></p>
<p style=\"margin-top:-10px; font-size: 10px; text-align:left; \">All Packs Freight Cargo, Inc.<br/>
6162 NW 74th Ave<br/>
Miami, Florida 33166<br/>
Tel (305) 592-4814</p>

<p>Allpacks Freight Cargo ha generado una solicitud de autorización para el traslado nacional de los siguientes warehouse: <b> $lsitawarehouse </b>. 
    En caso de no estar de acuerdo con esta solicitud deberá enviar un correo a info@allpacksfc.com solicitando su anulación.</p>

<p>Para llenar el formulario de Autorización de envíos haga clic <a href='" . $GLOBALS['config']['public_autorizacion'] . "?codCliente=" . $autorizacion->getId_cliente() . "&idAutorizacion=" . $autorizacion->getId() . "'><img src=\"" . $GLOBALS['config']['baseproy'] . "images/autorizacion/paso1.png\" alt=\"paso1\" /></a></p>
<p>Para visualizar el pago total del envío de su carga haga clic <a href='" . $GLOBALS['config']['public_pagos'] . "?codCliente=" . $autorizacion->getId_cliente() . "&idAutorizacion=" . $autorizacion->getId() . "'><img src=\"" . $GLOBALS['config']['baseproy'] . "images/autorizacion/paso2.png\" alt=\"paso2\" /></a></p>";

        $bodyMail .= "</body>
        </html>";
        if (!MailControl::$testing) {
            for ($m = 0; $m < count($mailCliente); $m++) {
                if ($mailCliente[$m] != "")
                    MailControl::sentMail($mailCliente[$m], "Allpacksfc.com --- Solicitud de autorización de envío No." . $autorizacion->getId() . " con empresa de transporte nacional", $bodyMail);
            }
        }
        // print_r($mailCliente);
        //echo ("mail: " . $bodyMail);
        return false;
    }

    public static function sendEnvioPaquete($autorizacion, $numeroguia, $numeropieza, $pesomercancia,$listadoCorreosAutorizados,$listadoImagenes,$empresa) {


        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
        $headers .= 'From: ' . MailControl::$noreplay . "\r\n";

        $bodyMail = "<html>
                    <body style='font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px'>

                    <p style=\"text-align:left; \"><img src=\"" . $GLOBALS['config']['baseproy'] . "images/All_packs_Logo.jpg\" alt=\"asegura\" height=\"129\" /></p>
                    <p style=\"margin-top:-10px; font-size: 10px; text-align:left; \">All Packs Freight Cargo, Inc.<br/>
                    6162 NW 74th Ave<br/>
                    Miami, Florida 33166<br/>
                    Tel (305) 592-4814</p>
                    <table>
                    <tbody>
                     <tr>
                    <td>Empresa de Envío:</td>
                    <td>" . $empresa . "</td>
                     </tr>
                     <tr>
                     <tr>
                    <td>Numero de Guía:</td>
                    <td>" . $numeroguia . "</td>
                     </tr>
                     <tr>
                    <td>Numero de Piezas:</td>
                    <td>" . $numeropieza . "</td>
                    </tr>
                    <tr>
                    <td>Peso de la mercancía:</td>
                    <td>" . $pesomercancia . "</td>
                    </tr>";
        if (count($listadoImagenes) > 0) {
            for ($m = 0; $m < count($listadoImagenes); $m++) {
                $bodyMail .="<tr><td><img src=\"" . $GLOBALS["config"]['rootProy'] . $GLOBALS["config"]['pathimagenthumbview'] . $listadoImagenes[$m] . "250x250.jpg\" alt=\"" . $listadoImagenes[$m] . "\"/></td><tr>";
            }
        }
        $bodyMail .="</tbody></table>";

        $bodyMail .= "</body>
        </html>";
        if (!MailControl::$testing) {
            for ($m = 0; $m < count($listadoCorreosAutorizados); $m++) {
                if ($listadoCorreosAutorizados[$m] != "")
                    MailControl::sentMail($listadoCorreosAutorizados[$m], "Allpacksfc.com --- Envio de pedio" . $autorizacion->getId() . " con empresa de transporte nacional", $bodyMail);
            }
            MailControl::sentMail("pagos@allpacksfc.com", "Allpacksfc.com --- Pago de autorización de envío No. " . $autorizacion->getId() . " con empresa de transporte nacional", $bodyMail);
        }

        error_log("mail: " . $bodyMail);
        return false;
    }

    public static function sendPagoAutorizacionTotal($autorizacion, $datosCliente, $pago, $tablewarehouse, $datos, $total, $tipo) {

        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
        $headers .= 'From: ' . MailControl::$noreplay . "\r\n";
        switch ($tipo) {
            case 1:
                $formapago = "bolivares";
                $medioPago = MailControl::pagoBS($pago);
                break;
            case 2:
                $formapago = "paypal";
                $medioPago = MailControl::pagoPayPal($pago);
                break;
            case 3:
                $formapago = "giftcards";
                $medioPago = MailControl::pagoGift($pago);
                break;
            case 4:
                $formapago = "cupo electronico";
                $medioPago = MailControl::pagoCupo($pago);
                break;
        }
        $bodyMail = "<html>
                    <body style='font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px'>

                    <p style=\"text-align:left; \"><img src=\"" . $GLOBALS['config']['baseproy'] . "images/All_packs_Logo.jpg\" alt=\"asegura\" height=\"129\" /></p>
                    <p style=\"margin-top:-10px; font-size: 10px; text-align:left; \">All Packs Freight Cargo, Inc.<br/>
                    6162 NW 74th Ave<br/>
                    Miami, Florida 33166<br/>
                    Tel (305) 592-4814</p>";
        $bodyMail .= "<div style=\"width: 100%;\">
            <p style=\"text-align: justify; font-size: 18px\">Medio de Pago: Total</p>
                    <p style=\"text-align: justify; font-size: 12px\">
                        El Cliente $datos (" . $autorizacion->getId_cliente() . ") ha solicitado realizar el pago del wh ($tablewarehouse)  y de la autorizacion (" . $autorizacion->getId() . ") con un monto estimado según sistema de $total a través de $formapago.
                    </p>";
        $bodyMail.=$medioPago;
        $bodyMail .= $autorizacion->getPathFact() != null ? "<p>Descargar facturas: <a href = '" . $GLOBALS['config']['baseproy'] . $GLOBALS['config']['pathfactview'] . "/" . $autorizacion->getPathFact() . "'> aqui</a></p>" : "";
        $bodyMail .= "</div>";
        $bodyMail .= "</body>
        </html>";
        if (!MailControl::$testing) {

            MailControl::sentMail($datosCliente->getEmail(), "Allpacksfc.com --- Pago de autorización de envío No. " . $autorizacion->getId() . " con empresa de transporte nacional", $bodyMail);
            return MailControl::sentMail("pagos@allpacksfc.com", "Allpacksfc.com --- Pago de autorización de envío No. " . $autorizacion->getId() . " con empresa de transporte nacional", $bodyMail);
        }

        error_log("mail: " . $bodyMail);
        return false;
    }

    public static function sendPagoAutorizacionParcial($autorizacion, $datosCliente, $pagoPC, $pagoPT, $tablewarehouse, $datos, $montoPC, $montoPT) {

        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
        $headers .= 'From: ' . MailControl::$noreplay . "\r\n";

        switch ($pagoPC->getTipo_pago()) {
            case 1:
                $formapagoPC = "bolivares";
                $medioPagoPC = MailControl::pagoBS($pagoPC);
                break;
            case 2:
                $formapagoPC = "paypal";
                $medioPagoPC = MailControl::pagoPayPal($pagoPC);
                break;
            case 3:
                $formapagoPC.= "giftcards";
                $medioPagoPC = MailControl::pagoGift($pagoPC);
                break;
            case 4:
                $formapagoPC = "cupo electronico";
                $medioPagoPC = MailControl::pagoCupo($pagoPC);
                break;
        }

        switch ($pagoPT->getTipo_pago()) {
            case 1:
                $formapagoPT = "bolivares";
                $medioPagoPT = MailControl::pagoBS($pagoPT);
                break;
            case 2:
                $formapagoPT = "paypal";
                $medioPagoPT = MailControl::pagoPayPal($pagoPT);
                break;
            case 3:
                $formapagoPT = "gift card";
                $medioPagoPT = MailControl::pagoGift($pagoPT);
                break;
            case 4:
                $formapagoPT = "cupo electronico";
                $medioPagoPT = MailControl::pagoCupo($pagoPT);
                break;
        }
        $bodyMail = "<html>
                    <body style='font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px'>

                    <p style=\"text-align:left; \"><img src=\"" . $GLOBALS['config']['baseproy'] . "images/All_packs_Logo.jpg\" alt=\"asegura\" height=\"129\" /></p>
                    <p style=\"margin-top:-10px; font-size: 10px; text-align:left; \">All Packs Freight Cargo, Inc.<br/>
                    6162 NW 74th Ave<br/>
                    Miami, Florida 33166<br/>
                    Tel (305) 592-4814</p>";
        $bodyMail .= "<div style=\"width: 100%;\">
            <p style=\"text-align: justify; font-size: 18px\">Medio de Pago: Parcial</p>
                    <p style=\"text-align: justify; font-size: 12px\">
                        El Cliente $datos (" . $autorizacion->getId_cliente() . ") ha solicitado realizar el pago del wh ($tablewarehouse) con un monto estimado según sistema de $montoPC a través de $formapagoPC.
                    </p>";

        $bodyMail.=$medioPagoPC;
        $bodyMail .="<p style=\"text-align: justify; font-size: 12px\">
                       Adicionalmente ha solicitado realizar el pago de la autorizacion No.(" . $autorizacion->getId() . ") con un monto estimado según sistema de $montoPT a través de $formapagoPT.
                    </p>";
        $bodyMail.=$medioPagoPT;
        $bodyMail .= $autorizacion->getPathFact() != null ? "<p>Descargar facturas: <a href = '" . $GLOBALS['config']['baseproy'] . $GLOBALS['config']['pathfactview'] . "/" . $autorizacion->getPathFact() . "'> aqui</a></p>" : "";
        $bodyMail .= "</div>";
        $bodyMail .= "</body>
        </html>";
        if (!MailControl::$testing) {
            
            MailControl::sentMail($datosCliente->getEmail(), "Allpacksfc.com --- Pago de autorización de envío No. " . $autorizacion->getId() . " con empresa de transporte nacional", $bodyMail);
            return MailControl::sentMail("pagos@allpacksfc.com", "Allpacksfc.com --- Pago de autorización de envío No. " . $autorizacion->getId() . " con empresa de transporte nacional", $bodyMail);
        }

        error_log("mail: " . $bodyMail);
        return false;
    }

    public static function sendConfirmarcionAutorizacion($idAutorizacion, $datos, $cedula, $telefono, $idempresa, $direccion, $monto) {
        $autorizacionControl = new AutorizacionControl();
        $envioControl = new EnvioControl();
        $listadowarehouse = $autorizacionControl->getWarehouseByAutorizacion($idAutorizacion);
        $listdoCorreos = $autorizacionControl->getCorreosByAutorizacion($idAutorizacion);
        $empresa = $envioControl = $envioControl->buscarEnvioById($idempresa);
        $monto = str_replace(".", "", $monto);
        $monto = str_replace(", ", ".", $monto);
        $valor = $empresa->getPorcentaje();
        $valor = $empresa->getPorcentaje() / 100;
        $total = $monto * $valor;

        $tablewarehpuse = "";
        for ($m = 0; $m < count($listadowarehouse); $m++) {
            $coma = "";
            if ($m + 1 < count($listadowarehouse))
                $coma = ", ";
            $tablewarehpuse.= $listadowarehouse[$m] . $coma;
        }


        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
        $headers .= 'From: ' . MailControl::$noreplay . "\r\n";

        $bodyMail = "<html>
        <body style = 'font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px'>

        <p style = \"text-align:left; \"><img src=\"" . $GLOBALS['config']['baseproy'] . "images/All_packs_Logo.jpg\" alt=\"asegura\" height=\"129\" /></p>
<p style=\"margin-top:-10px; font-size: 10px; text-align:left; \">All Packs Freight Cargo, Inc.<br/>
6162 NW 74th Ave<br/>
Miami, Florida 33166<br/>
Tel (305) 592-4814</p>
      <div>
     
      
                        <p style=\"font-size: 20px; text-align: center\">Autorización de envío con empresa de Transporte Nacional</p>
                        <p style=\"text-align: justify;font-size: 16 px;\">Yo <b> $datos </b>,
                            portador de la cédula de identidad numero  <b>$cedula</b>, 
                            propietario de la encomienda No. <b>'$tablewarehpuse'</b>,
                            autorizo a la empresa All Packs FC a realizar el envío de mi mercancía a través de la empresa  <b>" . $empresa->getDes() . "</b>.<br /><br />

                            Estimado cliente recuerde que el costo del envio nacional esta incluido en la tarifa de All Packs FC usted no tendra que cancelarlo,
                            sin embargo usted solicita asegurar su carga por <b>" . number_format($monto, 2, ",", ".") . "</b> Bsf, esto genera un cargo adicional de <b>" . number_format($total, 2, ",", ".") . "</b> Bsf, el cual sera incluido en la factura que le haremos llegar. Una vez pagada y confirmada se realizará el envío de su encomienda.<br /><br />

                            All Packs FC se compromete a enviar le al usuario el Recibo de la carga así como fotos de como fue enviada la misma, de tal manera que el usuario pueda verificar y comparar lo que reciba, 
                            en caso de haber alguna discrepancia en el embalaje, peso o contenido de la carga el cliente deberá formular el reclamo a la empresa de transporte autorizada (<b>" . $empresa->getDes() . "</b>).<br /><br />

                            Es responsabilidad del cliente conocer las condiciones de servicios de la empresa <b>" . $empresa->getDes() . "</b>,
                            así como las políticas de seguro y reposición en caso de perdidas.
                            <br /><br />
                            All Packs FC no tendra responsabilidad una vez la carga sea entregada a la empresa antes seleccionada y descrita por el cliente. </p>
                            <p style=\"text-align: justify;font-size: 16px;\"><b>Teléfono contacto: </b>" . $telefono . " </p>
                            <p style=\"text-align: justify;font-size: 16px;\"><b>Direccion de envío: </b>" . $direccion . " </p>
                           
                                
                            <p style=\"font-size: 20px; text-align: center\">Terminos y condiciones de la  empresa de Transporte: <b>" . $empresa->getDes() . "</b></p>
                            <p style=\"text-align: justify;font-size: 16px;\">" . $empresa->getTerminos() . "</p>
                                                               

                             <p style=\"text-align: justify\">Esta autorización ha sido completada por usted y ha sido registrada en nuestro sistema,
                     en pocos momentos procesaremos su envío con la empresa de transporte y le mantendremos informado. 
                     En caso que no haya realizado esta operación porfavor envíenos un correo ainfo@allpacksfc.com informando el error.</p>
                    </div>";

        $bodyMail .= "</body>
        </html>";
        if (!MailControl::$testing) {
            for ($m = 0; $m < count($listdoCorreos); $m++) {
                if ($listdoCorreos[$m] != "")
                    MailControl::sentMail($listdoCorreos[$m], "Allpacksfc.com --- Solicitud de autorización de envío No. " . $idAutorizacion . " con empresa de transporte nacional", $bodyMail);
            }
            MailControl::sentMail("pagos@allpacksfc.com", "Allpacksfc.com --- Solicitud de autorización de envío No. " . $idAutorizacion . " con empresa de transporte nacional", $bodyMail);
        }

        error_log("mail: " . $bodyMail);
        return false;
    }

    private static function sentMail($correoDestino, $asunto, $body) {

        if (!MailControl::$testing) {
            $headers = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
            $headers .= 'From: ' . MailControl::$noreplay . "\r\n";

            return mail($correoDestino, $asunto, $body, $headers);
            //return mail("info@allpacksfc.com", "Allpacksfc.com --- Solicitud de pago giftcard cliente $consigneecode y warehouse ", $bodyMail, $headers);
        }

        $correoSalida = array(MailControl::$noreplaytest => MailControl::$noreplayname);
        $transport = Swift_SmtpTransport::newInstance(MailControl::$SMTP, MailControl::$SMTP_PORT)
                ->setUsername(MailControl::$noreplaytest)
                ->setPassword(MailControl::$nopass);

        $mailer = Swift_Mailer::newInstance($transport);

        $message = Swift_Message::newInstance($asunto)
                ->setContentType('text/html')
                ->setFrom($correoSalida)
                ->setTo($correoDestino)
                ->setBody($body)
        ;

        //Send the message
        return $mailer->send($message);
    }

    private static function pagoBS($pago) {
        switch ($pago->getForma_pago()){
            case 1 :
                  $texto = "Descargar documento de transferencia";
                break;
            case 2 :
                  $texto = "Descargar documento de transferencia de la Carga";
                break;
            case 3 :
                  $texto = "Descargar documento de transferencia del Envio";
                break;
        }
          
            $texto = "Descargar documento de transferencia";
            $bodyMail = "<p>$texto: <a href = '" . $GLOBALS['config']['baseproy'] . $GLOBALS['config']['pathpagosview'] . "/" . $pago->getDes() . "'> aqui</a></p>";
        return $bodyMail;
    }

    private static function pagoPayPal($pago) {
        $bodyMail = "<p style=\"text-align: justify; font-size: 12px\">
                        El correo suministrado por el cliente para el envío de la solicitud de pago es <span style=\"font-size: 14px\">" . $pago->getDes() . "</span>
                    </p>";
        return $bodyMail;
    }

    private static function pagoGift($pago) {

        $bodyMail = "<p style=\"text-align: justify; font-size: 12px\">
                        El codigo de la giftcard suministrado por el cliente es: <span style=\"font-size: 14px\">" . $pago->getDes() . "</span>
                    </p>";
        return $bodyMail;
    }

    private static function pagoCupo($pago) {
            switch ($pago->getForma_pago()){
            case 1 :
                  $texto = "Descargar documento de transferencia";
                break;
            case 2 :
                  $texto = "Descargar documento de transferencia de la Carga";
                break;
            case 3 :
                  $texto = "Descargar documento de transferencia del Envio";
                break;
        }
        $bodyMail = "<p>$texto: <a href = '" . $GLOBALS['config']['baseproy'] . $GLOBALS['config']['pathpagosview'] . "/" . $pago->getDes() . "'> aqui</a></p>";
        return $bodyMail;
    }

}

?>