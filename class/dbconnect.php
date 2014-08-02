<?php

class dbconnect {
	function __construct(){}
	public function connect(){
		try {
			$DB = new PDO('mysql:dbname=schedule;host=localhost','root','Shiet1sv') or die('FAILED CONNECTION');
			$DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return $DB; 
		} catch (PDOException $e){
			return $e->getMessage();	
		}
	}
} 


?>