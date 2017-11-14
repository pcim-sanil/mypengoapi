<?php

/**
 * define environment
 * DEV, PRODUCTION
 */
define('ENVIRONMENT', 'DEV');

/**
 * Database configuration
 * adjust params based on enviornment
 */
define('DB_HOST', '192.168.0.120');
define('DB_USER', 'developer');
define('DB_PASSWORD', 'abc123');
define('DB_NAME', '1300_staging');
define('DB_CHARSET', 'utf8');


/**
 * path configuration
 */
define('BASE_PATH', __DIR__);
define('COMPONENTS_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'components');
define('MODELS_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'models');


/**
 * MyPengo configuraiton
 */
define('MYPENGO_BASE_URL_UNSUBSCRIBE', 'http://api.mypengo.io/mcm/customercare/unsubscribe');
//define('MYPENGO_BASE_URL_SUBSCRIPTION', 'http://api.mypengo.io/mcm/subscriptioninfo');
define('MYPENGO_BASE_URL_SUBSCRIPTION', 'http://localhost/pcimWork/mypengo/localArray.php');
