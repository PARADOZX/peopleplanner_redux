<?php

session_start();

include 'includes/config.inc.php';

$ctrl = ucfirst($_REQUEST['ctrl']) . 'Controller';

$action = strtolower($_REQUEST['action']);

try {
	$controller = new $ctrl();
	$controller->$action();
} catch (Exception $e) {
	echo $e->getMessage();
}

?>