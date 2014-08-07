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
	//check if GET action var is set to logout.  if so, log out.
	if (isset($_GET['action']) && ($_GET['action'] === 'logout')){
		User_Auth::logOut();
	} else {
		$dbquery = new dbquery($dbconnection->connect());
		$calender = new Calendar($dbquery->getAllDates());
		$calender->create();
		$calender->render();
	}

} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	//check if POST action var is set to password.  if so, login 
	if (isset($_POST['action']) && ($_POST['action'] === 'login')){
		$user_auth = new User_Auth($_POST['email'], $_POST['password']);
		
	} else {
		$dbquery = new dbquery($dbconnection->connect());
		$dbquery->toggleDate();
	}
}

?> 