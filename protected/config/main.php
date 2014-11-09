<?php

return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'preload' => array('log'),
    'import' => array(
        'application.models.*',
        'application.components.api.*',
        'application.components.*',
    ),
    'name' => 'ToysRus',
    'theme' => 'custom_theme',
    // Components shared over all environments
    'components' => array(
        'user' => array(
            // enable cookie-based authentication
            //'allowAutoLogin' => true,
            'identityCookie' => array(
                'httpOnly' => true,
            ),
        ),
        'urlManager' => array(
            'urlFormat' => 'path',
            'showScriptName' => false,
            'caseSensitive' => false,
            'urlSuffix'=>'.html',
            'rules' => array(
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ),
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning',
                    'categories' => 'system.*',
                ),
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning, trace, info',
                    'categories' => 'application.*',
                ),
                array(
                    'class' => 'CFileLogRoute',
                    'logFile' => 'mailer.log',
                    'categories' => 'appMailer',
                    'MaxFileSize' => 1024 * 2,
                    'maxLogFiles' => 1000
                ),
                array(
                    'class' => 'CFileLogRoute',
                    'logFile' => 'souq_api.log',
                    'categories' => 'souq_api',
                    'MaxFileSize' => 1024 * 2,
                    'maxLogFiles' => 1000,
                ),
                array(
                    'class' => 'CFileLogRoute',
                    'logFile' => 'payment.log',
                    'categories' => 'payment',
                    'MaxFileSize' => 1024 * 2,
                    'maxLogFiles' => 1000
                ),
                array(
                    'class' => 'CFileLogRoute',
                    'logFile' => 'payment_error.log',
                    'categories' => 'payment_error',
                    'MaxFileSize' => 1024 * 2,
                    'maxLogFiles' => 1000
                ),
                array(
                    'class' => 'CFileLogRoute',
                    'logFile' => 'payment_success.log',
                    'categories' => 'payment_success',
                    'MaxFileSize' => 1024 * 2,
                    'maxLogFiles' => 1000
                ),
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning',
                ),
            ),
        ),
    ),
    // Global params used in all environments
    'params' => array(
        'environment' => $this->_mode,
        'adminEmail' => 'info@souq.com',
        'country_iso_code' => 'ae',
        'CountryIsoCode' => 'ae',
        'country_landline_code' => '00971',
        'currency' => 'AED',
        'mainPath' => $_SERVER['DOCUMENT_ROOT'],
        'logNL' => "\n--------------------------",
        'phpass' => array(
            'iteration_count_log2' => 8,
            'portable_hashes' => false,
        ),
    ),
);
?>