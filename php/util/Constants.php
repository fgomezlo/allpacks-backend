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
        "hostname" => "localhost",
        "schema" => "syncstephy",
        "username" => "syncstephyuser",
        "password" => "SyncStephy2019"
    ]
];

$CONFIG['defaultservice'] = [
    "test" => 1,
    "prd" => 1
];

$CONFIG['consolidacionstatus'] = [
    "pendiente" => 1,
    "proceso" => 2,
    "completado" => 3,
    "anulado" => 4,
    "reempacado" => 5
];

$CONFIG['status'] = [
    "active" => [
        "db" => "1",
        "swal" => "success"
        ],
    "disabled" => [
        "db" => "0",
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

/* PHP Mailer CONFIG */
$CONFIG["mail"] = [
    "test" => [
        "smtp.server" => "smtp-relay.brevo.com",
        "smtp.port" => 587,
        "smtp.login" => "webmaster@allpacksfc.com",
        "smtp.pass" => "tf7g5Y0M1W4OFb8n",
        "noreply" => "noreply@allpacksfc.com",
        "noreply.name" => "No reply Allpacksfc Testing",
        "baseref" => "http://localhost:4200/#"
    ],
    "prd" => [
        "smtp.server" => "smtp-relay.brevo.com",
        "smtp.port" => 587,
        "smtp.login" => "webmaster@allpacksfc.com",
        "smtp.pass" => "tf7g5Y0M1W4OFb8n",
        "noreply" => "noreply@allpacksfc.com",
        "noreply.name" => "No reply Allpacksfc",
        "baseref" => "https://www.allpacksfc.com/admin-coreui/#"
    ]
];

// RECAPTCHA v2 Google
$CONFIG["recaptcha.secret-server"] = "6Ldx-18UAAAAAB0v7HgfRsm9yIafwZzjV-GB2UJN";

/* Pagination parameters*/
$CONFIG["limit"] = 10;

$GLOBALS["config"] = $CONFIG;
?>
