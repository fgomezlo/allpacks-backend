<?php

$CONFIG = array();

$CONFIG['env'] = "test"; // prd : for pruction , test: for testing

$CONFIG['database'] = [
    "test" => [
        "hostname" => "localhost",
        "schema" => "allpacksfc_final",
        "username" => "root",
        "password" => "root"
    ],
    "prd" => [
        "hostname" => "10.0.0.20",
        "schema" => "fvf_inventario",
        "username" => "wilman",
        "password" => "ww050609*"
    ]
];

$CONFIG['status'] = [
    "active" => [
        "db" => "active",
        "swal" => "success"
        ],
    "disable" => [
        "db" => "disable",
        "swal" => "success"
    ],
    "error" => [
        "db" => "error",
        "swal" => "error"
    ],
    "signup" => [
        "db" => "signup",
        "swal" => "success"
    ]
];

/* JWT Constants */
$CONFIG["jwt.prd"] = "@11p@ck5fc2024";
$CONFIG["jwt.test"] = "te5t#@g24";

/* MAILCHIMP-MANDRILL CONFIG */
$CONFIG["mandrill.apikey"] = "md-pDP4RfnppJxKRbwp1QDZiA";

/*$CONFIG["mandrill.template.welcome"] = "mercatech-welcome-message";
$CONFIG["mandrill.template.resetpass"] = "mercatech-reset-password";
$CONFIG["mandrill.template.newclientalert"] = "mercatech-new-client-alert";
$CONFIG["mandrill.template.publicmail"] = "mercatech-public-website";
*/
$CONFIG["mandrill.template.activate"] = "tio-san-activate-account";
$CONFIG["mandrill.activateurl"] = "http://localhost:4200/#/activate";

//Emails
$CONFIG["email.newclientalert"] = "mercatech2019@gmail.com";
$CONFIG["email.newclientalert2"] = "info@mercatech.net";


$GLOBALS["config"] = $CONFIG;
?>
