<?php

/*
	blogless - a blogless writing system
	Author:  Martin Doering <martin@datenbrei.de>
	Project: http://blogless.datenbrei.de
	License: http://blogless.datenbrei.de/license/
*/

	require_once('config.php');
	require('auth.php');

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$name = (isset($_POST['name']) && $_POST['name'] != '') ? strtolower($_POST['name']) : null;
		if ($name) {
			$dir = $config["basedir"] . DIRECTORY_SEPARATOR . $name . '/';
			header('Location: edit.php?article=' . urlencode($name));
		}
		else {
			$dir = $config["basedir"] . DIRECTORY_SEPARATOR;
			header('Location: edit.php');
		}
		@mkdir ($dir);
		$path = $dir . basename($_FILES["upload"]["name"]);
		move_uploaded_file($_FILES["upload"]["tmp_name"], $path);
	}
?>