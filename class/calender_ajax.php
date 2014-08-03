 <?php 
error_reporting('ALL');
date_default_timezone_set('America/New_York');

//add class auto_loader
include 'dbquery.php';
include 'calendar.php';

$dbconnection = new dbconnect();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	$dbquery = new dbquery($dbconnection->connect());
	$calender = new Calendar($dbquery->getDate());
	$calender->create();
	$calender->render();

} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$dbquery = new dbquery($dbconnection->connect());
	$dbquery->setDate();
}


?>