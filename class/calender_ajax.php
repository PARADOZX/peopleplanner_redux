 <?php 

session_start();

error_reporting('ALL');
date_default_timezone_set('America/New_York');

//add class auto_loader
include 'dbquery.php';
include 'calendar.php';
include 'user.php';
include 'user_auth.php';

$dbconnection = new dbconnect();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	$dbquery = new dbquery($dbconnection->connect());
	$calender = new Calendar($dbquery->getAllDates());
	$calender->create();
	$calender->render();

} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	//checks if action POST var is set.  if so new user is registering
	if (isset($_POST['action']) && ($_POST['action'] === 'password')){
		if (isset($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) && isset($_POST['password'])) {
	     $email = $_POST['email'];
	     $password = $_POST['password'];
	     User_Auth::check_login($email, $password);
		}
		
	} else {
		$dbquery = new dbquery($dbconnection->connect());
		$dbquery->toggleDate();
	}
}

?> 