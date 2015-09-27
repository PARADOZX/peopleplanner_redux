<?php

session_start();

include 'includes/config.inc.php';
// include 'class/AuthController.php';
// include 'class/AppController.php';
// include 'class/dbconnect.php';
// include 'class/Register.php';
// include 'class/user_auth.php';
// include 'class/dbquery.php';
// include 'class/usersquery.php';
// include 'class/calendar.php';

$ctrl = ucfirst($_REQUEST['ctrl']) . 'Controller';

$action = strtolower($_REQUEST['action']);

try {
	$controller = new $ctrl();
	$controller->$action();
} catch (Exception $e) {
	echo $e->getMessage();
}

?>