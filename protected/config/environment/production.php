<?php

## dynamic params file prefix to include.
        require(dirname(__FILE__) . '/params_loader.php');
        $prodParams = loadParamsConf('prod');


        $db_host = $prodParams['db_host'];
        unset($prodParams['db_host']);
        $db_name = $prodParams['db_name'];
        unset($prodParams['db_name']);
        $db_user = $prodParams['db_user'];
        unset($prodParams['db_user']);
        $db_pass = $prodParams['db_pass'];
        unset($prodParams['db_pass']);

        $memcache_host = $prodParams['memcache_host'];
        unset($prodParams['memcache_host']);
        $memcache_port = $prodParams['memcache_port'];
        unset($prodParams['memcache_port']);
        $memcache_id = $prodParams['memcache_id'];
        unset($prodParams['memcache_id']);


        //$session_save_path = "10.0.0.10:11211";
        //Session Save Path
        $session_save_path = $memcache_host . ":" . $memcache_port;
        ini_set('session.save_handler', 'memcached');
        ini_set('session.save_path', $session_save_path);

        //Database Configuration
        $confDB = array(
            'connectionString' => 'mysql:host=' . $db_host . ';dbname=' . $db_name,
            'emulatePrepare' => true,
            'username' => $db_user,
            'password' => $db_pass,
            'charset' => 'utf8',
            'enableParamLogging' => true,
            'enableProfiling' => true,
        );

        //Cache Configuration
        $confCACHE = array(
            'useMemcached' => true,
            'class' => 'system.caching.CMemCache',
            'servers' => array(
                array('host' => $memcache_host, 'port' => $memcache_port, 'weight' => 100),
            ),
        );

        //Session Cache Configuration
        $confSESSIONCACHE = array(
            'class' => 'system.caching.CMemCache',
            'servers' => array(
                array('host' => $memcache_host, 'port' => $memcache_port, 'weight' => 100),
            ),
        );

        ##
        return  array(
            'modules' => array(
                'admin',
            ),
            // application components
            'components' => array(
                // Database connection
                'db' => $confDB,
                'cache' => $confCACHE,
                'sessionCache' => $confSESSIONCACHE,
                'errorHandler' => array(
                    // use 'site/error' action to display errors
                    'errorAction' => 'site/error',
                ),
            ),
            // include dynamic params that can be updated in backoffice
            'params' => $prodParams,
        );