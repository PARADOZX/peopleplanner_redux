<?php

// include 'User.php'; //create this.

class Usersquery {
	protected $DB = '';

	function __construct(PDO $connect){
		//pass in DB connection
		if ($this->DB == '') $this->DB = $connect;
	}

	public function getUsers(){
		try {
			$q = "SELECT * FROM user";
			$stmt = $this->DB->prepare($q);
			$stmt->execute();
			$stmt->setFetchMode(PDO::FETCH_ASSOC);
			echo '<span>Attendees<span>';
			while ($result = $stmt->fetch()){
				echo '<br />' . "<div id='attendee_list'><div title='" . $result['name'] . "' style='background-color:" . $result['color'] . "' class='dot'></div>" . 
					$result['firstName'] . '</div>';
			}
		} catch (PDOException $e){
			echo $e->getMessage();
		}
	}
}

?>