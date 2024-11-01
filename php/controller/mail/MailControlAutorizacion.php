<?php

class MailControlAutorizacion {

    private static $noreplay = "Nohely Ramirez <nohely@allpacksfc.com>";
    private static $noreplaytest = "nohely@allpacksfc.com";
    private static $nopass = "Franpoto2014$";
    private static $noreplayname = "Nohely Ramirez";
    private static $testing = false;
    private static $SMTP = "smtp.1and1.com";
    private static $SMTP_PORT = 587;

    

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
        $headers .= 'From: ' . MailControlAutorizacion::$noreplay . "\r\n";

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
        if (!MailControlAutorizacion::$testing) {
            for ($m = 0; $m < count($mailCliente); $m++) {
                if ($mailCliente[$m] != "")
                    MailControlAutorizacion::sentMail($mailCliente[$m], "Allpacksfc.com --- Solicitud de autorización de envío No." . $autorizacion->getId() . " con empresa de transporte nacional", $bodyMail);
            }
        }
        // print_r($mailCliente);
        //echo ("mail: " . $bodyMail);
        return false;
    }

    public static function sendEnvioPaquete($autorizacion, $numeroguia, $numeropieza, $pesomercancia,$listadoCorreosAutorizados,$listadoImagenes,$empresa) {


        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
        $headers .= 'From: ' . MailControlAutorizacion::$noreplay . "\r\n";

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
        if (!MailControlAutorizacion::$testing) {
            for ($m = 0; $m < count($listadoCorreosAutorizados); $m++) {
                if ($listadoCorreosAutorizados[$m] != "")
                    MailControlAutorizacion::sentMail($listadoCorreosAutorizados[$m], "Allpacksfc.com --- Envio de pedio" . $autorizacion->getId() . " con empresa de transporte nacional", $bodyMail);
            }
            MailControlAutorizacion::sentMail("pagos@allpacksfc.com", "Allpacksfc.com --- Pago de autorización de envío No. " . $autorizacion->getId() . " con empresa de transporte nacional", $bodyMail);
        }

        error_log("mail: " . $bodyMail);
        return false;
    }

    public static function sendPagoAutorizacionTotal($autorizacion, $datosCliente, $pago, $tablewarehouse, $datos, $total, $tipo,$listadogitft) {

        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
        $headers .= 'From: ' . MailControlAutorizacion::$noreplay . "\r\n";
        switch ($tipo) {
            case 1:
                $formapago = "bolivares";
                $medioPago = MailControlAutorizacion::pagoBS($pago);
                break;
            case 2:
                $formapago = "paypal";
                $medioPago = MailControlAutorizacion::pagoPayPal($pago);
                break;
            case 3:
                $formapago = "giftcards";
                $medioPago = MailControlAutorizacion::pagoGift($listadogitft);
                break;
            case 4:
                $formapago = "cupo electronico";
                $medioPago = MailControlAutorizacion::pagoCupo($pago);
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
        if (!MailControlAutorizacion::$testing) {

            MailControlAutorizacion::sentMail($datosCliente->getEmail(), "Allpacksfc.com --- Pago de autorización de envío No. " . $autorizacion->getId() . " con empresa de transporte nacional", $bodyMail);
            return MailControlAutorizacion::sentMail("pagos@allpacksfc.com", "Allpacksfc.com --- Pago de autorización de envío No. " . $autorizacion->getId() . " con empresa de transporte nacional", $bodyMail);
        }

        error_log("mail: " . $bodyMail);
        return false;
    }

    public static function sendPagoAutorizacionParcial($autorizacion, $datosCliente, $pagoPC, $pagoPT, $tablewarehouse, $datos, $montoPC, $montoPT,$listadogitft) {

        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
        $headers .= 'From: ' . MailControlAutorizacion::$noreplay . "\r\n";

        switch ($pagoPC->getTipo_pago()) {
            case 1:
                $formapagoPC = "bolivares";
                $medioPagoPC = MailControlAutorizacion::pagoBS($pagoPC);
                break;
            case 2:
                $formapagoPC = "paypal";
                $medioPagoPC = MailControlAutorizacion::pagoPayPal($pagoPC);
                break;
            case 3:
                $formapagoPC = "giftcards";
                $medioPagoPC = MailControlAutorizacion::pagoGift($listadogitft);
                break;
            case 4:
                $formapagoPC = "cupo electronico";
                $medioPagoPC = MailControlAutorizacion::pagoCupo($pagoPC);
                break;
        }

        switch ($pagoPT->getTipo_pago()) {
            case 1:
                $formapagoPT = "bolivares";
                $medioPagoPT = MailControlAutorizacion::pagoBS($pagoPT);
                break;
            case 2:
                $formapagoPT = "paypal";
                $medioPagoPT = MailControlAutorizacion::pagoPayPal($pagoPT);
                break;
            case 3:
                $formapagoPT = "gift card";
                $medioPagoPT = MailControlAutorizacion::pagoGift($pagoPT->getDes());
                break;
            case 4:
                $formapagoPT = "cupo electronico";
                $medioPagoPT = MailControlAutorizacion::pagoCupo($pagoPT);
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
        if (!MailControlAutorizacion::$testing) {
            
            MailControlAutorizacion::sentMail($datosCliente->getEmail(), "Allpacksfc.com --- Pago de autorización de envío No. " . $autorizacion->getId() . " con empresa de transporte nacional", $bodyMail);
            return MailControlAutorizacion::sentMail("pagos@allpacksfc.com", "Allpacksfc.com --- Pago de autorización de envío No. " . $autorizacion->getId() . " con empresa de transporte nacional", $bodyMail);
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
        $headers .= 'From: ' . MailControlAutorizacion::$noreplay . "\r\n";

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
                     En caso que no haya realizado esta operación porfavor envíenos un correo a info@allpacksfc.com informando el error.</p>
                    </div>";

        $bodyMail .= "</body>
        </html>";
        if (!MailControlAutorizacion::$testing) {
            for ($m = 0; $m < count($listdoCorreos); $m++) {
                if ($listdoCorreos[$m] != "")
                    MailControlAutorizacion::sentMail($listdoCorreos[$m], "Allpacksfc.com --- Solicitud de autorización de envío No. " . $idAutorizacion . " con empresa de transporte nacional", $bodyMail);
            }
            MailControlAutorizacion::sentMail("pagos.admccs@allpacksfc.com", "Allpacksfc.com --- Solicitud de autorización de envío No. " . $idAutorizacion . " con empresa de transporte nacional", $bodyMail);
        }

        error_log("mail: " . $bodyMail);
        return false;
    }

    private static function sentMail($correoDestino, $asunto, $body) {

        if (!MailControlAutorizacion::$testing) {
            $headers = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
            $headers .= 'From: ' . MailControlAutorizacion::$noreplay . "\r\n";

            return mail($correoDestino, $asunto, $body, $headers);
            //return mail("info@allpacksfc.com", "Allpacksfc.com --- Solicitud de pago giftcard cliente $consigneecode y warehouse ", $bodyMail, $headers);
        }

    $correoSalida = array(MailControlAutorizacion::$noreplaytest => MailControlAutorizacion::$noreplayname);
        $transport = Swift_SmtpTransport::newInstance(MailControlAutorizacion::$SMTP, MailControlAutorizacion::$SMTP_PORT)
                ->setUsername(MailControlAutorizacion::$noreplaytest)
                ->setPassword(MailControlAutorizacion::$nopass);

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

    private static function pagoGift($listadogitft) {

        $bodyMail = "<p style=\"text-align: justify; font-size: 12px\">
                        El codigo de o las giftcard suministrado por el cliente son: <span style=\"font-size: 14px\">" . $listadogitft . "</span>
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