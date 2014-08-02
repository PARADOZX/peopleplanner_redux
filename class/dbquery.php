<?php

include 'dbconnect.php'; //DEBUGGER

class dbquery{
	protected $DB = '';
	protected $dummyVar = 8;  //month -- get this from AJAX data

	function __construct(PDO $connect){
		if ($this->DB == '') $this->DB = $connect;
	}

	public function getDate(){
		// $q = "SELECT EXTRACT(DAY FROM date) as date, EXTRACT(MONTH FROM date) as month FROM date WHERE dateID = ?";
		$q = "SELECT u.firstName, EXTRACT(DAY FROM d.date) as date FROM user as u INNER JOIN dateuser as du ON u.userID = du.userID INNER JOIN date as d ON d.dateID = du.dateID WHERE EXTRACT(MONTH FROM d.date) = ?";
		$stmt = $this->DB->prepare($q);
		$stmt->execute(array($this->dummyVar));
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$array = array();
		while ($result = $stmt->fetch()){
			$returnArray = array();
			$returnArray['firstName'] = $result['firstName'];
			$returnArray['date'] = $result['date'];
			$array[] = $returnArray;
		}
		return $array;
	}
}

$dbconnection = new dbconnect();
$dbquery = new dbquery($dbconnection->connect());
echo json_encode($dbquery->getDate());


?>