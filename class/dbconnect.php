<?php

class dbconnect {
	function __construct(){}
	public function connect(){
		try {
			$DB = new PDO('mysql:dbname=schedule;host=localhost','root','Shiet1sv') or die('FAILED CONNECTION');
			// $DB = new PDO('mysql:dbname=byjames1_schedule;host=localhost','byjames1_ling','Shiet1sv') or die('FAILED CONNECTION');
			
			$DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return $DB; 
		} catch (PDOException $e){
			return $e->getMessage();	
			die('Server connection error.');
		}
	}
} 


?>