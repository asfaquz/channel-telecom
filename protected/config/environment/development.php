<?php

require(dirname(__FILE__) . '/params_loader.php');
$envParams = loadParamsConf('dev');
$db_host = $envParams['db_host'];
unset($envParams['db_host']);
$db_name = $envParams['db_name'];
unset($envParams['db_name']);
$db_user = $envParams['db_user'];
unset($envParams['db_user']);
$db_pass = $envParams['db_pass'];
unset($envParams['db_pass']);

$memcache_host = $envParams['memcache_host'];
unset($envParams['memcache_host']);
$memcache_port = $envParams['memcache_port'];
unset($envParams['memcache_port']);
$memcache_id = $envParams['memcache_id'];
unset($envParams['memcache_id']);

$session_save_path = $memcache_host . ":" . $memcache_port;
ini_set('session.save_handler', 'memcached');
ini_set('session.save_path', $session_save_path);

$confDB = array(
    'connectionString' => 'mysql:host=' . $db_host . ';dbname=' . $db_name,
    'emulatePrepare' => true,
    'tablePrefix' => 'qst_',
    'username' => $db_user,
    'password' => $db_pass,
    'charset' => 'utf8',
);
/*
  //Cache Conf
  $confCACHE = array(
  'useMemcached' => true,
  'class' => 'system.caching.CMemCache',
  'servers' => array(
  array('host' => $memcache_host, 'port' => $memcache_port, 'weight' => 100),
  ),
  );
  //Session Cache Conf
  $confSESSIONCACHE = array(
  'class' => 'system.caching.CMemCache',
  'servers' => array(
  array('host' => $memcache_host, 'port' => $memcache_port, 'weight' => 100),
  ),
  );
 */

return array(
    'modules' => array(
        'admin',
        'gii' => array(
            'class' => 'system.gii.GiiModule',
            'password' => '123',
            'ipFilters' => array($_SERVER['REMOTE_ADDR'], '127.0.0.1', '::1'),
        ),
    ),
    'components' => array(
        'db' => $confDB,
        // 'cache' => $confCACHE,
        // 'sessionCache' => $confSESSIONCACHE,
        'errorHandler' => array(
            // use 'site/error' action to display errors
            'errorAction' => 'site/error_dev',
        ),
        'log' => array(
            'routes' => array(
                array(
                    'class' => 'CWebLogRoute',
                    'enabled' => $this->_debug,
                ),
            )
        )
    ),
    // include dynamic params that can be updated in backoffice
    'params' => $envParams,
);