<?php

class dbquery{
	protected $DB = '';
	protected $month;  
	protected $year;
	protected $day;
	protected $userID;
	public $table = '';      
	protected $array = '';    
	protected $tableKey;               
	
	function __construct($DB, $table='', $tableKey=''){     
		$this->DB = $DB;
		$this->table = $table;
		$this->tableKey = $tableKey;
	}

	public function getTableInfo(){
		if (!isset($_SESSION['user']) && !filter_var($_SESSION['user'], FILTER_VALIDATE_INT)){
			throw new Exception('A user has not signed in.');	
		} else {
			if (empty($this->table)) {
				//get table information from initial load.  retrieves information for the earliest table for the user
				try {
					$q = "SELECT tu.admin, tu.tableID, t.tripName, t.tableName FROM tableuser as tu INNER JOIN tableinfo as t ON tu.tableID = t.tableID where tu.userID = {$_SESSION['user']}";
					$stmt = $this->DB->query($q);
					$result = $stmt->fetch();
					
					//no tables associated with user (ie. just registered)
					if (!$result) {
						return false;
					}

				} catch (PDOException $e) {
					echo $e->getMessage();
				}
			} else {
				//get table information from all subsequent loads.
				try {
					$q = "SELECT tu.admin, tu.tableID, t.tripName, t.tableName FROM tableuser as tu INNER JOIN tableinfo as t ON tu.tableID = t.tableID where t.tableName = '$this->table' LIMIT 1";
					$stmt = $this->DB->query($q);
					$result = $stmt->fetch();
				} catch (PDOException $e) {
					echo $e->getMessage();
				}
			}

			if (empty($this->array)) $this->array = array();

			$returnArray = array();
			$returnArray['tableID'] = $result['tableID'];
			$returnArray['tripName'] = $result['tripName'];
			$returnArray['tableName'] = $result['tableName'];
			$returnArray['admin'] = $result['admin'];

			$this->array['tripInfo'][] = $returnArray;

			$this->table = $this->array['tripInfo'][0]['tableName'];

			try {
				$q = "SELECT * FROM `tableuser` as tu INNER JOIN tableinfo as ti ON tu.tableID = ti.tableID WHERE userID = {$_SESSION['user']}";
				$stmt = $this->DB->query($q);
				$returnArray2 = array();
				while ($result = $stmt->fetch()){
					$returnArray2['tableName'] = $result['tableName'];
					$returnArray2['tripName'] = $result['tripName'];
					$this->array['allTrips'][] = $returnArray2;
				}
			} catch (PDOException $e) {
				echo $e->getMessage();
			}
			return $this->table;
			
		}
	}

	public function getAllDates(){

			//define month for first load since not an AJAX call; otherwise month is determined by $_GET seen below
		$date = time();
		$month = date('m', $date);
		$year = date('Y', $date);

		//validate GET month var from AJAX call
		$this->month = (isset($_GET['month']) && filter_var($_GET['month'], FILTER_VALIDATE_INT, array("options"=>
		array("min_range"=>1, "max_range"=>12)))) ? $_GET['month'] : $month;

		$this->year = (isset($_GET['year']) && filter_var($_GET['month'], FILTER_VALIDATE_INT)) ? $_GET['year'] : $year;

		$q = "SELECT u.firstName, EXTRACT(DAY FROM d.date) as date, u.color FROM user as u INNER JOIN $this->table as du ON u.userID = du.userID INNER JOIN date as d ON d.dateID = du.dateID WHERE EXTRACT(YEAR FROM d.date) = ? AND EXTRACT(MONTH FROM d.date) = ?";
		$stmt = $this->DB->prepare($q);
		$stmt->execute(array($this->year, $this->month));
		$stmt->setFetchMode(PDO::FETCH_ASSOC);

		if (empty($this->array)) $this->array = array();

		$this->array['uniqueDates'] = array();
		
		while ($result = $stmt->fetch()){
			$returnArray = array();
			$returnArray['firstName'] = $result['firstName'];
			$returnArray['date'] = $result['date'];
			$returnArray['color'] = $result['color'];
			$this->array['info'][] = $returnArray;
			//pushes date if unique to array
			if (!in_array($result['date'], $this->array['uniqueDates'])) $this->array['uniqueDates'][] = $result['date'];
		}
		// return json_encode($array);

		
		return $this->array;
	}

	public function toggleDate(){
		if (isset($_POST['month']) && filter_var($_POST['month'], FILTER_VALIDATE_INT, array("options"=>
		array("min_range"=>1, "max_range"=>12)))) $this->month = $_POST['month'];

		if (isset($_POST['day']) && filter_var($_POST['day'], FILTER_VALIDATE_INT)) $this->day = $_POST['day'];	

		if (isset($_POST['year']) && filter_var($_POST['year'], FILTER_VALIDATE_INT)) $this->year = $_POST['year'];

		if (isset($_POST['userID']) && filter_var($_POST['userID'], FILTER_VALIDATE_INT)) $this->userID = $_POST['userID'];

		$date = $_POST['year'] . '-' . $_POST['month'] . '-' . $_POST['day'];
		$date = trim($date);

		// DEBUGGER
		// echo $_POST['day'] . ' ' . $_POST['month'] . ' ' . $_POST['year'] . ' ' . $_POST['userID'];
		// echo $date;
		// $date = '2014-08-13';

		$q = "SELECT * FROM user as u INNER JOIN $this->table as du ON u.userID = du.userID INNER JOIN date as d ON d.dateID = du.dateID WHERE d.date = ? AND u.userID = ?";
		try {

			$stmt = $this->DB->prepare($q);
			$stmt->execute(array($date, $this->userID));
			$stmt->setFetchMode(PDO::FETCH_ASSOC);
			$result = $stmt->fetch();
			//returns the date-user (many to many) DB ID
			$dateUserID = $result['dateUserID'];

			//if user is already associated with this selected date.
			if ($result) {
				//disassociate user from selected date
				$q = "DELETE FROM $this->table WHERE dateUserID = ?";
				$stmt= $this->DB->prepare($q);
				$stmt->execute(array($dateUserID));
				echo true;

			} else {
				$q = "SELECT * FROM date WHERE date = ?";
				$stmt = $this->DB->prepare($q);
				$stmt->execute(array($date));
				$result = $stmt->fetch();

				//if selected date is already created
				if ($result) {
					//associate user to selected date
					$dateID = $result['dateID'];
					$q = "INSERT INTO $this->table (userID, dateID) VALUES ($this->userID, $dateID)";
					$stmt = $this->DB->prepare($q);
					$stmt->execute();
					echo true;
				} else {
					//if selected date has not been created create date
					$q = "INSERT INTO date (date) VALUES (?)";
					$stmt = $this->DB->prepare($q);
					$stmt->execute(array($date));
					$lastID = $this->DB->lastInsertId();

					//associate user to selected date
					$q = "INSERT INTO $this->table (userID, dateID) VALUES ($this->userID, $lastID)";
					$stmt = $this->DB->prepare($q);
					$stmt->execute();
					echo true;
				}
			}

		} catch (PDOException $e) {
			echo 'SERVER CONNECTION ERROR';
			//echo $e->getMessage();  //DEBUGGER
		} 
	}

	public function getTableByKey(){
		try {
			$q = "SELECT tableName, tableID from tableinfo WHERE keyID = ?";
			$stmt = $this->DB->prepare($q);
			$stmt->execute(array($this->tableKey));
			$result = $stmt->fetch();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}

		if ($result) {
			$tableID = $result['tableID'];
			try {
				
				$result = $this->DB->query("SELECT userID FROM tableuser WHERE tableID = $tableID AND userID = {$_SESSION['user']}");
				
				if ($result->rowCount() < 1){
					$result = $this->DB->query("INSERT INTO tableuser (tableID, userID) VALUES ($tableID, {$_SESSION['user']})");
					return false;
				} else {
					echo 'You already joined this trip.';
				}

			} catch (PDOException $e) {
				echo $e->getMessage();
			}	
		} else {
			echo "Unknown trip ID.  Try again.";
		}
	}

	public function getUsers(){
		try {
			$q = "SELECT u.userID, u.firstName, u.email, u.color FROM `tableinfo` as ti INNER JOIN tableuser as tu ON ti.tableID = tu.tableID INNER JOIN user as u ON tu.userID = u.userID WHERE ti.tableName = '$this->table'";
			$stmt = $this->DB->prepare($q);
			$stmt->execute();
			$stmt->setFetchMode(PDO::FETCH_ASSOC);

			$usersArray = array();

			while($result = $stmt->fetch()){
				array_push($usersArray, $result);
			}

			return $usersArray;

		} catch (PDOException $e){
			echo $e->getMessage();
		}
	}

	public function setNewTrip($newTripName){

		try{
			date_default_timezone_set('America/New_York');
			$date = date('mdYhis');

			$tableName = strtolower(substr(preg_replace('/\s+/', '', $newTripName), 0, 3)) . $date;
			
			// $newTripName = $this->DB->quote($newTripName);  //do not uncomment this.

			$q = "CREATE TABLE " . $tableName . " (dateUserID int auto_increment, dateID int, userID int, primary key(dateUserID))";	
			$stmt = $this->DB->query($q);

			if ($stmt) {

				$characters = 'abcdefghijklmnopqrstuvwxyz0123456789';

				$keyID = '';
				for ($i = 0; $i < 10; $i++) {
				      $keyID .= $characters[rand(0, strlen($characters) - 1)];
				}

				$q = "INSERT INTO tableinfo (tableName, tripName, keyID) VALUES (?, ?, ?)";
				$stmt = $this->DB->prepare($q);
				$result = $stmt->execute(array($tableName, $newTripName, $keyID));

				if ($result) {
					$lastID = $this->DB->lastInsertId();
					$result = $this->DB->query("INSERT INTO tableuser (tableID, userID, admin) VALUES ($lastID, {$_SESSION['user']}, 1)");
					
					if ($result) {
						echo 'Table created successfully';
					}
				}

			} else echo 'ERROR';

		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function sendInvite(){
		$table = $_POST['table'];
		$email = $_POST['email'];

		//run query .. retrieve table keyID by tableID

		$subject = 'You have been invited';
		$message = 'This is my email string.  YOu are hereby invited to join the group Pigu';
	
		//mail($email, $subject, $string);
	}

	public function tooltip($date){
		$result = $this->DB->query("SELECT firstName FROM user as u INNER JOIN tableuser as tu ON u.userID = tu.userID INNER JOIN tableinfo as ti ON tu.tableID = ti.tableID WHERE ti.tableName = '$this->table'");
		//retrieves number of users associated with this table.  should MOVE THIS QUERY so that it's not queried each time theres a unique date.
		$tooltipInfo = array();
		while($num = $result->fetch()){
			$tooltipInfo['usernames'][] = $num['firstName'];
		}
		
		$q = "SELECT u.firstName, EXTRACT(DAY FROM d.date) as date FROM user as u INNER JOIN $this->table as du ON u.userID = du.userID INNER JOIN date as d ON d.dateID = du.dateID WHERE d.date = '$date'";
		$stmt = $this->DB->query($q);
		while ($result = $stmt->fetch()){
			$tooltipInfo['attending'][] = $result['firstName'];
		}

		$tooltipInfo['notattending'] = array_diff($tooltipInfo['usernames'], $tooltipInfo['attending']);

		$tooltipInfo['count_coming'] = count($tooltipInfo['usernames']) - count($tooltipInfo['notattending']);

		$tooltipInfo['count_not_coming'] = count($tooltipInfo['notattending']);

		
		return $tooltipInfo;

	}
}

?>