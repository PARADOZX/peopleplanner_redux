<?php

include 'dbconnect.php'; //DEBUGGER

class dbquery{
	protected $DB = '';
	protected $month;  

	function __construct(PDO $connect){
		//pass in DB connection
		if ($this->DB == '') $this->DB = $connect;

		//define month for first load since not an AJAX call; otherwise month is determined by $_GET seen below
		$date = time();
		$month = date('m', $date);
		$year = date('Y', $date);

		//validate GET month var from AJAX call
		$this->month = (isset($_GET['month']) && filter_var($_GET['month'], FILTER_VALIDATE_INT, array("options"=>
		array("min_range"=>1, "max_range"=>12)))) ? $_GET['month'] : $month;

		$this->year = (isset($_GET['year']) && filter_var($_GET['month'], FILTER_VALIDATE_INT)) ? $_GET['year'] : $year;

	}

	public function getDate(){
		// $q = "SELECT EXTRACT(DAY FROM date) as date, EXTRACT(MONTH FROM date) as month FROM date WHERE dateID = ?";
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
}

//DEBUGGER
// $dbconnect = new dbconnect();
// $dbquery = new dbquery($dbconnect->connect());
// $array = json_encode($dbquery->getDate());
// print_r($array);
?>