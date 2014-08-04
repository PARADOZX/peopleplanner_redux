<?php

include 'dbconnect.php'; //DEBUGGER

class dbquery{
	protected $DB = '';
	protected $month;  
	protected $year;
	protected $day;
	protected $userID;

	function __construct(PDO $connect){
		//pass in DB connection
		if ($this->DB == '') $this->DB = $connect;

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

		$q = "SELECT u.firstName, EXTRACT(DAY FROM d.date) as date, u.color FROM user as u INNER JOIN dateuser as du ON u.userID = du.userID INNER JOIN date as d ON d.dateID = du.dateID WHERE EXTRACT(YEAR FROM d.date) = ? AND EXTRACT(MONTH FROM d.date) = ?";
		$stmt = $this->DB->prepare($q);
		$stmt->execute(array($this->year, $this->month));
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$array = array();
		// $array['info'] = '';
		$array['uniqueDates'] = array();
		while ($result = $stmt->fetch()){
			$returnArray = array();
			$returnArray['firstName'] = $result['firstName'];
			$returnArray['date'] = $result['date'];
			$returnArray['color'] = $result['color'];
			$array['info'][] = $returnArray;
			//pushes date if unique to array
			if (!in_array($result['date'], $array['uniqueDates'])) $array['uniqueDates'][] = $result['date'];
		}
		// return json_encode($array);
		return $array;
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

		$q = "SELECT * FROM user as u INNER JOIN dateuser as du ON u.userID = du.userID INNER JOIN date as d ON d.dateID = du.dateID WHERE d.date = ? AND u.userID = ?";

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
				$q = 'DELETE FROM dateuser WHERE dateUserID = ?';
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
					$q = "INSERT INTO dateuser (userID, dateID) VALUES ($this->userID, $dateID)";
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
					$q = "INSERT INTO dateuser (userID, dateID) VALUES ($this->userID, $lastID)";
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
}

//DEBUGGER
// $dbconnect = new dbconnect();
// $dbquery = new dbquery($dbconnect->connect());
// $array = json_encode($dbquery->getDate());
// print_r($array);
?>