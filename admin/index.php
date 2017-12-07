<?php
/*
	blogless - a blogless writing system
	Author:  Martin Doering <martin@datenbrei.de>
	Project: http://blogless.datenbrei.de
	License: http://blogless.datenbrei.de/license/
*/

	// Set internal character encoding to 'UTF-8' - needed for some functions below
	// Not needed since PHP 5.6 with default_charset = UTF-8

	
	require_once('config.php');
	if ($config["basedir"] == '') {
		require('check.php');
	}		
	require('auth.php');
	require('include.php');
	
	// locale and our own path
	//define ('MYPATH', ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] );

	if ($_SERVER["REQUEST_METHOD"] == "GET") {
		$html = "<!DOCTYPE html> \n";
		$html .= "<html> \n";
		$html .= "<head> \n";
		$html .= "<title>Index of Articles</title> \n";
		$html .= "<meta charset=UTF-8> \n";
		$html .= '<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">' . "\n";
		$html .= '<link rel="stylesheet" href="admin.css" type="text/css" media="all">' . "\n";
		$html .= "</head> \n";
		$html .= "<body> \n";
		$html .= "<main> \n";
		$html .= "<h1>Index of Articles</h1> \n";
		$html .= '<br>' . "\n";
		
		$article = get_article(null);

		$html .= '<a class="page" href="edit.php" title="Edit">🔧</a>';
		$html .= '<a class="page" href="' . $config["baseurl"] . '" title="View Site Index" target="_blank">🔎 </a>' . "\n";
		$html .= ' &#8212; ';
		$html .= '<a class="page" href="' . $config["baseurl"] . '" title="View Site Index" target="_blank">' . $article['title'] . '</a><br />' . "\n";
		$html .= '<br />' . "\n";


		$files = get_article_list();
		foreach ($files as $name) {
			$article = get_article($name);
			$html .= '<a class="page" href="edit.php?article=' . urlencode($name) . '" title="Edit ' . urlencode($name) . '">🔧</a>';
			$html .= '<a class="page" href="delete.php?article=' . urlencode($name) . '" title="Delete ' . urlencode($name) . '">✖ </a>';
			$html .= ' &#8212; ';
			$html .= '<a class="page" href="' . $config["baseurl"] . urlencode($name) . '/" title="View ' . urlencode($name) . '" target="_blank">' . $article['title'] . '</a><br />' . "\n";
		}

		$html .= "</main> \n";
		$html .= '<nav id="botnav">';
		$html .= '<a class="menu" id="new" href="edit.php?article=' . date('Y-m-d', time()) . '">New Article</a>';
		$html .= '<a class="menu" id="settings" href="settings.php">Settings</a>';
		$html .= '<a class="menu" id="logout" href="logout.php">Logout</a>';
		$html .= '</nav>' . "\n";

		$html .= "</body>" . "\n";
		$html .= "</html>";

		header('Cache-Control: no-cache, must-revalidate');
		die($html);
	}
?>