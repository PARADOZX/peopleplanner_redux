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
		$calender = new Calendar($DBquery->getAllDates(), $tableName, $this->DB); 
		$calender->create();
		$calender->render();
	}

	public function toggledate()
	{
		$DBquery = new dbquery($this->DB, $this->table);
		$DBquery->toggleDate();
	}

	public function getuserlist()
	{
		$usersquery = new Usersquery($this->DB, $this->table);
		$usersquery->getUsers();
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
