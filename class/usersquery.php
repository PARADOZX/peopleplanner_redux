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
			$q = "SELECT u.userID, u.firstName, u.email, u.color FROM `tableinfo` as ti INNER JOIN tableuser as tu ON ti.tableID = tu.tableID INNER JOIN user as u ON tu.userID = u.userID WHERE ti.tableName = '$this->table'";
			$stmt = $this->DB->prepare($q);
			$stmt->execute();
			$stmt->setFetchMode(PDO::FETCH_ASSOC);
			// echo '<div id="create_join_menu">
			// 	    <div onclick="pages.renderpage('. "'#user_list, #calendar', '#start_page', pages.createtrip()" .')">Create New Trip</div><br />
			// 	    <div onclick="pages.renderpage('. "'#user_list, #calendar', '#start_page', pages.jointrip()" .')">Join An Existing Trip</div><br />
			// 	  </div>';
			echo '<div id="users_display"><b>Attendees</b><hr/>';
			while ($result = $stmt->fetch()){
				echo '<br />' . "<div class='attendees'>" . $result['firstName'] . "<div title='" . $result['firstName'] . "' style='background-color:" . $result['color'] . "' class='attendees_dot'></div></div>";
			}
			echo '</div>';
		} catch (PDOException $e){
			echo $e->getMessage();
		}
	}
}

?>