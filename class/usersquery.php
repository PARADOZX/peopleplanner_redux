<?php

include 'User.php'; //create this.

class usersquery {
	protected $DB = '';

	function __construct(PDO $connect){
		//pass in DB connection
		if ($this->DB == '') $this->DB = $connect;
	}

	// public function getUsers(){
	// 	$q = "SELECT * FROM user";
	// 	$stmt = $this->$DB->prepare($q);
	// 	$stmt->execute();
	// 	$stmt->setFetchMode(PDO::FETCH_CLASS, 'User');

	// 	$stmt->fetchAll();
	// }
}

?>