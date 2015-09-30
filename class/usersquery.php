<?php

class Usersquery {
	protected $DB = '';
	protected $table;

	function __construct(PDO $connect, $table){
		$this->DB = $connect;
		$this->table = $table;
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
}

?>