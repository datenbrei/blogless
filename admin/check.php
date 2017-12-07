<?php
/*
	blogless - a blogless writing system
	Author:  Martin Doering <martin@datenbrei.de>
	Project: http://blogless.datenbrei.de
	License: http://blogless.datenbrei.de/license/
*/

	$ok = true;

	function check_version() {
		$version = current(explode("-", PHP_VERSION));
		if (version_compare($version, '5.5.0') >= 0) {
			$html = '<p style="color:green;">☑  — Your PHP version ' . $version . ' is ok!</p>' . "\n";
			$ok = false;
		}
		else {
			$html = '<p style="color:red;">☒  — You have PHP version ' . $version . ', which is not sufficient. You need at least version 5.5 because of support for "password_verify"!</p>' . "\n";
		}

		return $html;
		}
		
	function check_file($filename) {
		global $ok;
		if (file_exists($filename)) {
			if (is_writable($filename)) {
				$html = '<p style="color:green;">☑  —  "' . $filename . '" exists and is writable.</p>' . "\n";
			}
			else {
				$html = '<p style="color:red;">☒  —  "' . $filename . '" with permissions ' . decoct(fileperms($filename) & 0777) . ' exists, but is not writable by webserver user "' . get_current_user(). '"!</p>' . "\n";
				$ok = false;
			}
		}
		else {
			if (touch($filename))
				$html = '<p style="color:green;">☑  —  "' . $filename . '" does not exist, but could be created by Blogless later...</p>' . "\n";
			else { 
				$html = '<p style="color:red;">☒  —  "' . $filename . '" does not exist, and can not be created by webserver user "' . get_current_user(). '" because of wrong directory permissions!</p>' . "\n";
				$ok = false;
			}
			unlink($filename);
		}
		
		return $html;
	}
	
	function check_dir($usage, $filename) {
		global $ok;
		if (is_dir($filename)) {
			if (is_writable($filename)) {
				$html = '<p style="color:green;">☑  — The ' . $usage . ' Directory "' . realpath($filename) . '" exists and is writable.</p>' . "\n";
			}
			else {
				$html = '<p style="color:red;">☒  — The ' . $usage . ' Directory "' . realpath($filename) . '" with permissions ' . decoct(fileperms($filename) & 0777) . ' exists, but is not writable by webserver user "' . get_current_user(). '"!</p>' . "\n";
				$ok = false;
			}
		}
		else {
			$ok = false;
			if (mkdir($filename))
				$html = '<p style="color:red;">☒  — The ' . $usage . ' Directory "' . realpath($filename) . '" does not exist, but could be created by Blogless later...</p>' . "\n";
			else 
				$html = '<p style="color:red;">☒  — The ' . $usage . ' Directory "' . realpath($filename) . '" does not exist, and can not be created by webserver user "' . get_current_user(). '" because of wrong directory permissions!</p>' . "\n";
			unlink($filename);
		}
		
		return $html;
	}

	function check_sessions() {
		global $ok;
		$html = '';
		
		if (session_status() == PHP_SESSION_NONE) {
			if (session_start()) { 
					$html = '<p style="color:green;">☑  — The PHP session handler is correctly configured and operational for Blogless</p>' . "\n";
			}
			else {
				$ok = false;
				$html = '<p style="color:red;">☒  — The PHP session handler is not configured correctly! No session can be initiated! Blogless will not work!</p>' . "\n";
				if (ini_get('session.save_handler') != '') {
					$html .= '<p style="color:black;">☝  — The "session.save_handler" in your php.ini is set to "' . ini_get('session.save_handler') . '".</p>' . "\n";
					if (ini_get('session.save_path') != '')
						$html .= '<p style="color:black;">☝  — The "session.save_path" in your php.ini is set to "' . ini_get('session.save_path') . '".</p>' . "\n";
					else
						$html .= '<p style="color:black;">☝  — The "session.save_path" in your php.ini is not set at all - but should be set!</p>' . "\n";
				}
				else {
					$html .= '<p style="color:black;">☝  — The "session.save_handler" in your php.ini is not set at all - but should be set!</p>' . "\n";
				}
			}
		}
		else {
			$html = '<p style="color:green;">☑  — The PHP session handler is correctly configured and operational for Blogless</p>' . "\n";
		}
		return $html;
	}

	if ($_SERVER["REQUEST_METHOD"] == "GET") {
		
		$html = "<html> \n";
		$html .= "<head> \n";
		$html .= "<title>Check Installation</title> \n";
		$html .= "<meta charset=UTF-8> \n";
		$html .= '<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">' . "\n";
		$html .= '<link rel="stylesheet" href="admin.css" type="text/css" media="all">' . "\n";
		$html .= "</head> \n";
		$html .= "<body> \n";
		$html .= '<header>' . "\n";
		$html .= '<h1>Check PHP Setup for Blogless</h1>' . "\n";
		$html .= '</header>' . "\n";

		$html .= '<h2>Check PHP Setup</h2>' . "\n";
		$html .= check_version();
		$html .= check_sessions();
		$html .= '<h2>Check Directory Permissions</h2>' . "\n";
		$html .= check_dir('Admin', '.');
		$html .= check_dir('Themes', 'themes/');
		$html .= check_dir('Article', '../articles/');
		$html .= '<h2>Check File Permissions</h2>' . "\n";
		$html .= check_file('config.php');
		$html .= check_file('password.php');
		$html .= check_file('check.php');
		$html .= '<hr>' . "\n";
		
		if ($ok) {
			$html .= '<p>Everything ok.</p>' . "\n";
			$html .= '<br>' . "\n";
			$html .= '<p><a id="save" href="settings.php">Go on and do some needed settings by clicking here!</a></p>' . "\n";
		}
		else {
			$html .= '<p>Your Installation will not run Blogless! Check and fix your setup and run this test again by clicking below!</p>' . "\n";
			$html .= '<br>' . "\n";
			$html .= '<p><a id="save" href="index.php">Check again!</a></p>' . "\n";
		}
		$html .= "</body>";
		$html .= "</html>";
		header('Content-type: text/html; charset=utf-8');
		header('Cache-Control: no-cache, must-revalidate');
		die($html);
	}
?>
