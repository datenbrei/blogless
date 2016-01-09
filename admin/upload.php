<?php

/*
	blogless - a blogless writing system
	Author:  Martin Doering <martin@datenbrei.de>
	Project: http://blogless.datenbrei.de
	License: http://blogless.datenbrei.de/license.html
*/

	require_once('config.php');
	require('auth.php');

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$name = (isset($_POST['name']) && $_POST['name'] != '') ? strtolower($_POST['name']) : '';
		if ($name == '')
			$dir = '../';
		else
			$dir = '../' . $name . '/';
			
		@mkdir ($dir);
		$path = $dir . basename($_FILES["upload"]["name"]);
		move_uploaded_file($_FILES["upload"]["tmp_name"], $path);
		header('Location: edit.php?article=' . urlencode($name));
	}
?>