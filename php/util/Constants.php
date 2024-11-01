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

/* PHP Mailer CONFIG */
$CONFIG["mail.smtp.server"] = "smtp-relay.brevo.com";
$CONFIG["mail.smtp.port"] = 587;
$CONFIG["mail.smtp.login"] = "webmaster@allpacksfc.com";
$CONFIG["mail.smtp.pass"] = "tf7g5Y0M1W4OFb8n";
$CONFIG["mail.noreply"] = "noreplay@allpacksfc.com";
$CONFIG["mail.noreply.name"] = "No reply Allpacksfc";
$CONFIG["mail.baseref"] = "http://localhost:4200/#";

// RECAPTCHA v2 Google
$CONFIG["recaptcha.secret-server"] = "6Ldx-18UAAAAAB0v7HgfRsm9yIafwZzjV-GB2UJN";

$GLOBALS["config"] = $CONFIG;
?>
