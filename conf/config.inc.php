<?php


error_reporting('ALL');


date_default_timezone_set('America/New_York');


function class_autoloader($class)
{
	include 'class/' . $class . '.php';
}

spl_autoload_register('class_autoloader');


define ( 'DB_HOST', 'localhost' );
define ( 'DB_USER', 'root' );
define ( 'DB_PASSWORD', '' );
define ( 'DB_DB', 'schedule' );