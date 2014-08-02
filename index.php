<?php

include 'class/calender.php';

//place in config
date_default_timezone_set('America/New_York');

$calender = new Calendar();

$calender->create();
$calender->render();

?>