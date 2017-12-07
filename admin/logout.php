<?php
/*
	blogless - a blogless writing system
	Author:  Martin Doering <martin@datenbrei.de>
	Project: http://blogless.datenbrei.de
	License: http://blogless.datenbrei.de/license/
*/

	session_start();
	if ($_SERVER["REQUEST_METHOD"] == "GET") {
		session_destroy();
		header('Location: login.php');

	}
?>
