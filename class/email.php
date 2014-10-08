<?php

class Email
{
	protected $email;
	protected $message;

	public function __construct()
	{

	}

	public function setEmail($email)
	{
		$this->email = $email;
	}

	public function setMessage($message)
	{
		$this->message = $message;
	}

	public function sendEmail()
	{
		
	}
}


?>