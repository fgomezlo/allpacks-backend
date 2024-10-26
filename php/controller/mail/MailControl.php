<?php

class MailControl {

    function enviarMailRecoveryPass($variables) {

        
        $mailchimp = new MailchimpTransactional\ApiClient();
        $mailchimp->setApiKey($GLOBALS["config"]["mandrill.apikey"]);
        
        
        //$mandrill = new Mandrill($GLOBALS["config"]["mandrill.apikey"]);
        $template_name = $GLOBALS["config"]["mandrill.template.resetpass"];
        $template_content = array(
            array(
                'name' => 'example name',
                'content' => 'example content',
            )
        );

        $message = array(
            "subject" => "Mercatech - solicitud de recuperación de contraseña",
            "from_email" => "no-reply@softecca.com",
            'to' => array(
                array(
                    'email' => $variables["user.mail"],
                    'name' => $variables["user.name"],
                    'type' => 'to'
                )
            ),
            'global_merge_vars' => array(
                array(
                    'name' => 'NAME',
                    'content' => $variables["user.name"]
                ),
                array(
                    'name' => 'EMAIL',
                    'content' => $variables["user.mail"]
                ),
                array(
                    'name' => 'URL_ACTIVATE',
                    'content' => $GLOBALS["config"]["url_activate"] . "?code=" . $variables["user.passwd"]
                )
            ),
        );

        $async = false;
        $ip_pool = 'Main Pool';

        $result = $mailchimp->messages->sendTemplate(
                [
                    "message"=>$message,
                    "template_name"=>$template_name,
                    "template_content" => $template_content
                ]
                );
        
        return $result;
    }

    function sendWelcomeMessage(User $user) {
        
        // $mandrill = new Mandrill($GLOBALS["config"]["mandrill.apikey"]);
        $mailchimp = new MailchimpTransactional\ApiClient();
        $mailchimp->setApiKey($GLOBALS["config"]["mandrill.apikey"]);
        
        $template_name = $GLOBALS["config"]["mandrill.template.welcome"];
        $template_content = array(
            array(
                'name' => 'example name',
                'content' => 'example content',
            )
        );

        $message = array(
            "subject" => "Registro de usuario exitoso , Bienvenido a Mercatech",
            "from_email" => "no-reply@softecca.com",
            'to' => array(
                array(
                    'email' => $user->getEmail(),
                    'name' => $user->getNombre(),
                    'type' => 'to'
                )
            ),
            'global_merge_vars' => array(
                array(
                    'name' => 'NAME',
                    'content' => $user->getNombre()
                ),
                array(
                    'name' => 'EMAIL',
                    'content' => $user->getEmail()
                )
            ),
        );

        $async = false;
        $ip_pool = 'Main Pool';

        $result = $mailchimp->messages->sendTemplate(
                [
                    "message"=>$message,
                    "template_name"=>$template_name,
                    "template_content" => $template_content
                ]
                );
        
        return $result;

    }

    function sendActivationMessage(User $user, $code) {
 
        $mailchimp = new MailchimpTransactional\ApiClient();
        $mailchimp->setApiKey($GLOBALS["config"]["mandrill.apikey"]);
        
        $template_name = $GLOBALS["config"]["mandrill.template.activate"];
        $template_content = array(
            array(
                'name' => 'example name',
                'content' => 'example content',
            )
        );

        $message = array(
            "subject" => "Confirmación de correo Mercatech",
            "from_email" => "no-reply@softecca.com",
            'to' => array(
                array(
                    'email' => $user->getEmail(),
                    'name' => $user->getDes(),
                    'type' => 'to'
                )
            ),
            'global_merge_vars' => array(
                array(
                    'name' => 'NAME',
                    'content' => $user->getDes()
                ),
                array(
                    'name' => 'EMAIL',
                    'content' => $user->getEmail()
                ),
                array(
                    'name' => 'URL_ACTIVATE',
                    'content' => $GLOBALS["config"]["mandrill.activateurl"] . "?code=" . $code
                )
            ),
        );
        // error_log(print_r($message, true));
        $async = false;
        $ip_pool = 'Main Pool';

        $result = $mailchimp->messages->sendTemplate(
                [
                    "message"=>$message,
                    "template_name"=>$template_name,
                    "template_content" => $template_content
                ]
                );
        
        return $result;

    }

    function sendNewClientAlert(User $user) {
        $mailchimp = new MailchimpTransactional\ApiClient();
        $mailchimp->setApiKey($GLOBALS["config"]["mandrill.apikey"]);
        
        $template_name = $GLOBALS["config"]["mandrill.template.newclientalert"];
        $template_content = array(
            array(
                'name' => 'example name',
                'content' => 'example content',
            )            
        );

        $message = array(
            "subject" => "Nuevo Registro de cliente exitoso.",
            "from_email" => "no-reply@softecca.com",
            'to' => array(
                array(
                    'email' => $GLOBALS["config"]["email.newclientalert"],
                    'name' => "Mercatech",
                    'type' => 'to'
                ),
                array(
                    'email' => $GLOBALS["config"]["email.newclientalert2"],
                    'name' => "Mercatech",
                    'type' => 'to'
                )
            ),
            'global_merge_vars' => array(
                array(
                    'name' => 'NAME',
                    'content' => $user->getNombre()
                ),
                array(
                    'name' => 'EMAIL',
                    'content' => $user->getEmail()
                ),
                array(
                    'name' => 'DOC',
                    'content' => $user->getContact_doc()
                ),
                array(
                    'name' => 'RIF',
                    'content' => $user->getCompany_doc()
                ),
                array(
                    'name' => 'COMPANY',
                    'content' => $user->getCompany_name()
                ),
                array(
                    'name' => 'ADDRESS',
                    'content' => $user->getAddress()
                ),
                array(
                    'name' => 'CITY',
                    'content' => $user->getCity()
                ),
                array(
                    'name' => 'PHONE',
                    'content' => $user->getPhone()
                ),
                array(
                    'name' => 'IMG',
                    'content' => $user->getImg_logo()
                ),
            ),
        );

        $async = false;
        $ip_pool = 'Main Pool';
        
        $result = $mailchimp->messages->sendTemplate(
                [
                    "message"=>$message,
                    "template_name"=>$template_name,
                    "template_content" => $template_content
                ]
                );
        
        return $result;
    }
    
    function sendPublicMessageAlert($post) {
        $mailchimp = new MailchimpTransactional\ApiClient();
        $mailchimp->setApiKey($GLOBALS["config"]["mandrill.apikey"]);
        
        $template_name = $GLOBALS["config"]["mandrill.template.publicmail"];
        $template_content = array(
            array(
                'name' => 'example name',
                'content' => 'example content',
            )
        );

        $message = array(
            "subject" => "Mercatech - Pregunta / Comentario pagina web.",
            "from_email" => "no-reply@softecca.com",
            'to' => array(
                array(
                    'email' => $GLOBALS["config"]["email.newclientalert"],
                    'name' => "Mercatech",
                    'type' => 'to'
                ),
                [
                    'email' => $GLOBALS["config"]["email.newclientalert2"],
                    'name' => "Mercatech",
                    'type' => 'to'
                ]
            ),
            'global_merge_vars' => array(
                array(
                    'name' => 'NAME',
                    'content' => $post['name']
                ),
                array(
                    'name' => 'EMAIL',
                    'content' => $post['mail']
                ),
                array(
                    'name' => 'MESSAGE',
                    'content' => $post["message"]
                )
            ),
        );

        $async = false;
        $ip_pool = 'Main Pool';
        $result = $mailchimp->messages->sendTemplate(
                [
                    "message"=>$message,
                    "template_name"=>$template_name,
                    "template_content" => $template_content
                ]
                );
        
        $message["subject"] = "Mercatech - Pregunta / Comentario pagina web Recibido";
        $message["to"] = [[
                    'email' => $post['mail'],
                    'name' => $post['name'],
                    'type' => 'to'
                ]];
        $result = $mailchimp->messages->sendTemplate(
                [
                    "message"=>$message,
                    "template_name"=>$template_name,
                    "template_content" => $template_content
                ]
                );
        return $result;
    }

}

?>