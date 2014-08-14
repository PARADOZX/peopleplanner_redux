<?php

// include 'User.php'; //create this.

class Usersquery {
	protected $DB = '';
	protected $table;

	function __construct(PDO $connect, $table){
		//pass in DB connection
		if ($this->DB == '') $this->DB = $connect;
		$this->table = $table;
	}

	public function getUsers(){
		try {
			// $q = "SELECT * FROM user";
			$q = "SELECT u.userID, u.firstName, u.email, u.color FROM `tableinfo` as ti INNER JOIN tableuser as tu ON ti.tableID = tu.tableID INNER JOIN user as u ON tu.userID = u.userID WHERE ti.tableName = '$this->table'";
			$stmt = $this->DB->prepare($q);
			$stmt->execute();
			$stmt->setFetchMode(PDO::FETCH_ASSOC);
			echo '<span>Attendees</span>';
			while ($result = $stmt->fetch()){
				echo '<br />' . "<div id='attendee_list'><div title='" . $result['firstName'] . "' style='background-color:" . $result['color'] . "' class='dot'></div>" . 
					$result['firstName'] . '</div>';
			}
		} catch (PDOException $e){
			echo $e->getMessage();
		}
	}
}

?>