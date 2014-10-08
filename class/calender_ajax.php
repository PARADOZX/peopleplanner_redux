 <?php 

session_start();

error_reporting('ALL');
date_default_timezone_set('America/New_York');

//add class auto_loader
include 'dbquery.php';
include 'calendar.php';
include 'user_auth.php';
include 'usersquery.php';
include 'register.php';
//include 'email.php';
// include 'test.php';     				//DELETE DEBUGG

$dbconnection = new dbconnect();		

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	//check if GET action var is set to logout.  if so, log out.
	if (isset($_GET['action']) && ($_GET['action'] === 'logout')){

		User_Auth::logOut();

	} else if (isset($_GET['action']) && ($_GET['action'] === 'userlist')){

		$usersquery = new Usersquery($dbconnection->connect(), $_GET['table']);
		$usersquery->getUsers();

	} else if (isset($_GET['action']) && ($_GET['action'] === 'join')){

		$usersquery = new dbquery($dbconnection->connect(), '', $_GET['tableKey']);
		$usersquery->getTableByKey();

	} else if (!isset($_GET['action']) && !empty($_GET['table'])) {

		$dbquery = new dbquery($dbconnection->connect(), $_GET['table']);  
		$tableName = $dbquery->getTableInfo();
		$calender = new Calendar($dbquery->getAllDates(), $tableName); 
		$calender->create();
		$calender->render();

	} else if (!isset($_GET['action'])){		

		$dbquery = new dbquery($dbconnection->connect());  
		$tableName = $dbquery->getTableInfo();

		if($tableName != false) {
			$calender = new Calendar($dbquery->getAllDates(), $tableName); 
			$calender->create();
			$calender->render();
		} else {
		
			return false;
		}
	}
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	//check if POST action var is set to password.  if so, login 
	if (isset($_POST['action']) && ($_POST['action'] === 'login')){
		$user_auth = new User_Auth($dbconnection->connect(), $_POST['email'], $_POST['password']);
	} else if (isset($_POST['action']) && ($_POST['action'] === 'register')){ 
		try {			
			$register = new Register($dbconnection->connect(), $_POST['firstName'], $_POST['email'], $_POST['password']);
		} catch (Exception $e) {
			echo 'whatwhat';
		}			
	} else if (isset($_POST['action']) && ($_POST['action'] === 'new')){

		$usersquery = new dbquery($dbconnection->connect());
		$usersquery->setNewTrip($_POST['newTripName']);
	} else if (isset($_POST['action']) && ($_POST['action'] === 'send_invite')){
		$dbquery = new dbquery($dbconnection->connect(), $_POST['table']);
		// $dbquery->send_invite();
		echo $_POST['email'] . ' ' . $_POST['table'];
		mail("jcc9283@hotmail.com", "test", "this is a test");
	} else {
		$dbquery = new dbquery($dbconnection->connect(), $_POST['table']);
		$dbquery->toggleDate();
	}
}


?> 