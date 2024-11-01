<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Description of AuthMailControl
 *
 * @author franciscogomezlopez
 */
class AuthMailControl {

    //put your code here
    

    public function sendRecoveryPass(User $user, $link) {
        $env = $GLOBALS['config']['env'];
        $mail = new PHPMailer(true);

        $mail->isSMTP();
//Enable SMTP debugging
//SMTP::DEBUG_OFF = off (for production use)
//SMTP::DEBUG_CLIENT = client messages
//SMTP::DEBUG_SERVER = client and server messages
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
//Set the hostname of the mail server
        $mail->Host = $GLOBALS["config"]["mail"][$env]["smtp.server"];

//Set the SMTP port number - likely to be 25, 465 or 587
        $mail->Port = $GLOBALS["config"]["mail"][$env]["smtp.port"];
//Whether to use SMTP authentication
        $mail->SMTPAuth = true;
//Username to use for SMTP authentication
        $mail->Username = $GLOBALS["config"]["mail"][$env]["smtp.login"];
//Password to use for SMTP authentication
        $mail->Password = $GLOBALS["config"]["mail"][$env]["smtp.pass"];
//Set who the message is to be sent from
        $mail->setFrom($GLOBALS["config"]["mail"][$env]["noreply"], $GLOBALS["config"]["mail"][$env]["noreply.name"]);
//Set an alternative reply-to address
//$mail->addReplyTo('replyto@example.com', 'First Last');
//Set who the message is to be sent to
        $mail->addAddress($user->getEmail(), $user->getDes());
//Set the subject line
        $mail->Subject = 'Allpacksfc - solicitud reinicio de clave administrativo';
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body

        $message = preg_replace(
                ["{{URL_ACTIVATE}}", 
                    "{{CURRENT_YEAR}}",
                    "{{BRAND_LOGO}}"], 
                [$GLOBALS["config"]["mail"][$env]["baseref"] . "/passrecovery/" . $link, 
                    date('Y'),
                    "https://www.allpacksfc.com/wp-content/uploads/2019/08/LOGO--1024x466.png"], 
                file_get_contents(__DIR__ . "/html/passrecovery.html"));
        
        //error_log($message);
        
        $mail->msgHTML($message, __DIR__);
//Replace the plain text body with one created manually
        $mail->AltBody = 'This is a plain-text message body';
//        $mail->Body = 'This is a plain-text message body';
//Attach an image file
//$mail->addAttachment('images/phpmailer_mini.png');
//SMTP XCLIENT attributes can be passed with setSMTPXclientAttribute method
//$mail->setSMTPXclientAttribute('LOGIN', 'yourname@example.com');
//$mail->setSMTPXclientAttribute('ADDR', '10.10.10.10');
//$mail->setSMTPXclientAttribute('HELO', 'test.example.com');
//send the message, check for errors

       /* if (!$mail->send()) {
            error_log('Mailer Error: ' . $mail->ErrorInfo);
        } else {
            error_log('Message sent!');
        }
        * 
        * 
        */
        
        try {
            return $mail->send();
        } catch (Exception $e) {
            error_log(print_r($mail, true));
        }
        return false;
    }

}
