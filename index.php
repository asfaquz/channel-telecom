<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
require_once(dirname(__FILE__) . '/protected/config/environment.php');
$environment = new Environment(Environment::DEVELOPMENT);
$yii = dirname(__FILE__) . '/framework/yii.php';
defined('YII_DEBUG') or define('YII_DEBUG', $environment->getDebug());
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', $environment->getTraceLevel());
require_once($yii);
Yii::createWebApplication($environment->getConfig())->run();

