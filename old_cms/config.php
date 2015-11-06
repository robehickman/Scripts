<?php

/*++++++++++++++++ Database configuration +++++++++++++++++*/
// Host name
    define("HOST",         "");       

// Database username
    define("USERNAME",     "");             

// Database password
    define("PASSWORD",     ""); 

// Database name
    define("DB_NAME",      "");        

/*++++++++++++++++ Framework configuration ++++++++++++++++*/
// Default controller
    define('DEFAULT_CONTROLLER', 'members');

    define('ALLOW_REGISTRATION', false);

    define('APP_MODE', 'test');

    define('AFFILIATE_RATE', '30');

    define('ADMIN_EMAIL', '');

    define('NOTIFY_EMAIL', '');

    define('TMPFILES', '../tmpfiles');

    $GLOBALS['allowed_ext'] = array('jpg', 'jpeg', 'png', 'gif');

    $GLOBALS['allowed_ext_files'] = array_merge($GLOBALS['allowed_ext'],
        array('txt', 'pdf', 'doc', 'odt', 'docx'));

