<?php

class AuthController 
{
	private $DB;

	public function __construct()
	{
		$DB = new dbconnect();
		$this->DB = $DB->connect();
	}

	public function register()
	{
		$register = new Register($this->DB, $_POST['firstName'], $_POST['email'], $_POST['password']);
		$register->registerUser();
	}

	public function login()
	{
		$user_auth = new User_Auth($this->DB, $_POST['email'], $_POST['password']);
		$user_auth->loginUser();
	}

	public function logout()
	{
		User_Auth::logOut();
	}
}