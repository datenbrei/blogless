<?php
/*
	blogless - a blogless writing system
	Author:  Martin Doering <martin@datenbrei.de>
	Project: http://blogless.datenbrei.de
	License: http://blogless.datenbrei.de/license/
*/

	// Check Login
	session_start();
	if (is_readable('password.php')) 
		@include 'password.php';
	else
		$password = false;

	if (empty($_SESSION['login']) or $_SESSION['login'] != $password) {
		header('Location: login.php');
		die("Access denied");
	}
?>
