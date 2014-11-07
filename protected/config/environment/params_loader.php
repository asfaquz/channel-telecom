<?php
function loadParamsConf($env) {
   if (!in_array($env, array('dev', 'stg', 'prod'))) {
		die('[2] No application environment selected!');
	}
	$file = dirname(__FILE__) . '/params/params_' . $env . '.inc';
        if($env=='dev'){
            include $file;
            return $aDefaultParams;
        }
	$content = @file_get_contents($file);
	if ($content === FALSE) {
		die('Server error [3248]');
	}
	$arr = unserialize(base64_decode($content));
	return $arr;
}



?>