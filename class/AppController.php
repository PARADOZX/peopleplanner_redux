<?php

class AppController
{
	private $DB;

	public function __construct()
	{
		$DB = new dbconnect();
		$this->DB = $DB->connect();

		if(isset($_GET['table'])) $this->table = $_GET['table'];
		if(isset($_POST['table'])) $this->table = $_POST['table'];

		if(isset($_POST['newTripName'])) $this->newTripName = $_POST['newTripName'];

		if(isset($_GET['tableKey'])) $this->tableKey = $_GET['tableKey'];

	}

	public function loadtable()
	{
		$DBquery = new dbquery($this->DB, $this->table);  
		$tableName = $DBquery->getTableInfo();

		$data = array();
		$calendar = new Calendar($DBquery->getAllDates(), $tableName, $this->DB); 


		//v.2
		$returnArray = $calendar->getCurrentMonthDetails();
		$data['DBdata'] = $returnArray['DBdata'];
		$data['daysInMonth'] = $returnArray['daysInMonth'];
		$data['blank'] = $returnArray['blank'];
		$data['year'] = $returnArray['year'];
		$data['month'] = $returnArray['month'];
		$data['title'] = $returnArray['title'];
		$data['dbquery'] = $returnArray['dbquery'];
		//v.2
		$returnArrayCounter = $calendar->monthYearCounter();  
		$data['nextYear'] = $returnArrayCounter['nextYear'];
		$data['previousYear'] = $returnArrayCounter['previousYear'];
		$data['nextMonth'] = $returnArrayCounter['nextMonth'];	
		$data['previousMonth'] = $returnArrayCounter['previousMonth'];	

		// $calendar->monthYearCounter();		//v.1

		//v.2
		$view = new View('calendar_view');
    	$view->render($data);

		//v.1
		// $calender->create(); //redirected.  obsolete
		// $calendar->render();
	}

	public function toggledate()
	{
		$DBquery = new dbquery($this->DB, $this->table);
		$DBquery->toggleDate();
	}

	public function getuserlist()
	{
		$usersquery = new Usersquery($this->DB, $this->table);
		$usersArray = $usersquery->getUsers();

		$view = new View('user_list_view');
    	$view->render($usersArray);
	}

	public function createnewtrip()
	{
		$DBquery = new dbquery($this->DB);
		$DBquery->setNewTrip($this->newTripName);
	}

	public function joinexistingtrip()
	{
		$DBquery = new dbquery($this->DB, '', $this->tableKey);
		$DBquery->getTableByKey();
	}

	public function sendinvite()
	{
		$DBquery = new dbquery($this->DB, $this->table);
		$DBquery->sendInvite();
	}
}
