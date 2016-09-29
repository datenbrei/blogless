<?php
/*
	blogless - a blogless writing system
	Author:  Martin Doering <martin@datenbrei.de>
	Project: http://blogless.datenbrei.de
	License: http://blogless.datenbrei.de/license.html
*/

	require('include.php');
	require('auth.php');
	
	function rrmdir($dir) { 
		foreach(glob($dir . '/*') as $file) { 
			if(is_dir($file)) rrmdir($file); else unlink($file); 
		} 
		rmdir($dir); 
	}

	if ($_SERVER["REQUEST_METHOD"] == "GET") {
		$article = (isset($_GET['article'])) ? $_GET['article'] : NULL; 
		$file = (isset($_GET['file'])) ? $_GET['file'] : NULL; 
		
		$html = "<html> \n";
		$html .= "<head> \n";
		$html .= "<title>Delete</title> \n";
		$html .= "<meta charset=UTF-8> \n";
		$html .= '<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">' . "\n";
		$html .= '<link rel="stylesheet" href="delete.css" type="text/css" media="all">' . "\n";
		$html .= '</head>' . "\n";
		$html .= '<body>' . "\n";
		$html .= '<main>' . "\n";
		$html .= '<article>' . "\n";

		if ($article && $file) {
			$html .= '<header>' . "\n";
			$html .= '<h1>Delete File</h1>' . "\n";
			$html .= '</header>' . "\n";
			$html .= '<p><blockquote>' . $file . '</blockquote></p>' . "\n";
			$html .= '<form method="post" action="delete.php" autocomplete="off">' . "\n";
			$html .= '<input type="hidden" name="article" value="' . $article . '">' . "\n";
			$html .= '<input type="hidden" name="file" value="' . $file . '">' . "\n";
			$html .= '<input id="save" name="choice" type="submit" value="Delete"> ';
			$html .= '<input id="save" name="choice" type="submit" value="Cancel">';
			$html .= '</form>' . "\n";
		}
		elseif ($article) {
			$html .= '<header>' . "\n";
			$html .= '<h1>Delete Article</h1>' . "\n";
			$html .= '</header>' . "\n";
			$html .= '<p><blockquote>' . $article . '</blockquote></p>' . "\n";
			$html .= '<form method="post" action="delete.php" autocomplete="off">';
			$html .= '<input type="hidden" name="article" value="' . $article . '">' . "\n";
			$html .= '<input id="save" name="choice" type="submit" value="Delete"> ';
			$html .= '<input id="save" name="choice" type="submit" value="Cancel">';
			$html .= '</form>';
		}
		else {
			header('Location: index.php');
		}

		$html .= '</article>' . "\n";
		$html .= '</main>' . "\n";
		$html .= '</body>';
		$html .= '</html>';
		header('Content-type: text/html; charset=utf-8');
		header('Cache-Control: no-cache, must-revalidate');
		die($html);
	}
	elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
		$article = filter_input(INPUT_POST, "article", FILTER_SANITIZE_STRING);
		$file = filter_input(INPUT_POST, "file", FILTER_SANITIZE_STRING);
		$choice = filter_input(INPUT_POST, "choice", FILTER_SANITIZE_STRING);
	
	
		if ($choice == 'Delete') {
			if ($article && $file && $file != 'index.html') {
				unlink('../' . $article . '/' . $file);
				header('Location: edit.php?article=' . urlencode($article));
			}
			elseif ($file && $file != 'index.html') {
				unlink('../' . $file);
				header('Location: edit.php');
			}
			elseif ($article) {
				rrmdir('../' . urldecode($article));
				header('Location: index.php');
			}
			else
				header('Location: index.php');
		}
		else {
			if ($article && $file && $file != 'index.html') {
				header('Location: edit.php?article=' . urlencode($article));
			}
			elseif ($file && $file != 'index.html') {
				header('Location: edit.php');
			}
			else
				header('Location: index.php');
			
		}
	}
?>
