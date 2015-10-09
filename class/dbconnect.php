<?php

class dbconnect {

	public function __construct(){}
	
	public function connect(){
		try {
			$DB = new PDO('mysql:dbname=' . DB_DB . ';host=' . DB_HOST, DB_USER, DB_PASSWORD) or die('FAILED CONNECTION');
			// $DB = new PDO('mysql:dbname=byjames1_schedule;host=localhost','byjames1_ling','********') or die('FAILED CONNECTION');
			
			$DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return $DB; 
		} catch (PDOException $e){
			return $e->getMessage();	
			die('Server connection error.');
		}
	}
} 


?>