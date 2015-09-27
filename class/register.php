<?php

class Register {
	private $DB;
	private $firstName;
	private $email;
	private $password;
	private $colors = array('blue', 'red', 'green', 'yellow', 'orange', 'purple', 'black', 'gray', 'olive', 'yellowgreen', 'pink');

	function __construct($DB, $firstName, $email, $password){
		//add validation 
		$this->DB = $DB;
		$this->firstName = $firstName;
		$this->email = $email;
		$this->password = $password;
		// $this->connect();	
	}

	public function registerUser(){
		try {
			$q = "SELECT color FROM user";
			$result = $this->DB->query($q);

			$colorsArray = array();
			
			while ($num = $result->fetch()){
				$colorsArray[] = $num['color'];
			}

			// //determine remaining colors available
			$colorsRemain = array_diff($this->colors, $colorsArray);

			$q = "INSERT INTO user (firstName, email, password, color) VALUES (?, ?, ?, ?)";
			$stmt = $this->DB->prepare($q);

			//UNCOMMENT - DEBUG
			$result = $stmt->execute(array($this->firstName, $this->email, password_hash($this->password, PASSWORD_BCRYPT), reset($colorsRemain)));

			//DELETE - DEBUG //password hash is not supported by bluehost since ver. 5.4 vs 5.5 on localhost
			// $result = $stmt->execute(array($this->firstName, $this->email, $this->password, reset($colorsRemain)));

			if ($result) {
				echo 'Registration Successful.  Login above.';
			} else echo 'Registration Error';
		
		} catch (PDOException $e) {
			// echo $e->getMessage();
			if ($e->getCode() == 23000){	//error code 23000 : duplicate entry
				echo $this->email . ' already registered.';
			}
		}

	}


}


?>