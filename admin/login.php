<?php
/*
	blogless - a blogless writing system
	Author:  Martin Doering <martin@datenbrei.de>
	Project: http://blogless.datenbrei.de
	License: http://blogless.datenbrei.de/license.html
*/

	// Is there yet a password set? If yes, get it.
	session_start();
	if (is_readable('password.php'))
		@include 'password.php';
	else
		$password = false;

	define('PWD_MESSAGE', 			'Login with Username and Password');
	define('PWD_SET',				'Initially set Username and global Password');
	define('PWD_ERROR',				'Wrong Username or Password - try again!');

	if ($_SERVER["REQUEST_METHOD"] == "GET") {
		$html = "<html> \n";
		$html .= "<head> \n";
		$html .= "<title>Login</title> \n";
		$html .= "<meta charset=UTF-8> \n";
		$html .= '<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">' . "\n";
		$html .= '<link rel="stylesheet" href="admin.css" type="text/css" media="all">' . "\n";
		$html .= '</head>' . "\n";
		$html .= '<body>' . "\n";
		$html .= '<main>' . "\n";
		$html .= '<article>' . "\n";

		if ($password) {
			$error = (filter_input(INPUT_GET, "error", FILTER_VALIDATE_INT)) ? true : false;

			$html .= '<header>' . "\n";
			$html .= '<h1>Login</h1>' . "\n";
			$html .= '</header>' . "\n";
			$html .= ($error) ? '<p>' . PWD_ERROR . '</p>' . "\n" : '<p>' . PWD_MESSAGE . '</p>' . "\n";
			$html .= '<br>' . "\n";
			$html .= '<form method="post" action="login.php" autocomplete="off">';
			$html .= '<p><input type="text" name="username" autofocus placeholder="Your Username"></p>' . "\n";
			$html .= '<p><input type="password" name="password" placeholder="Type your Password"></p>';
			$html .= '<br>' . "\n";
			$html .= '<p><input id="save" type="submit" value="Login">';
			$html .= '</form>';
		}
		else {
			$html .= '<header>' . "\n";
			$html .= '<h1>Set Password</h1>' . "\n";
			$html .= '</header>' . "\n";
			$html .= '<p>' . PWD_SET . '</p>' . "\n";
			$html .= '<br>' . "\n";
			$html .= '<form method="post" action="login.php" autocomplete="off">' . "\n";
			$html .= '<p><input type="text" name="username" autofocus placeholder="Choose a Username"></p>' . "\n";
			$html .= '<p><input type="password" name="password" placeholder="Set initial Password - Minimum 8 Characters"></p>' . "\n";
			$html .= '<br>' . "\n";
			$html .= '<p><input id="save" type="submit" value="Initial Login">';
			$html .= '</form>' . "\n";
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
		$pw = filter_input(INPUT_POST, "password", FILTER_SANITIZE_STRING,FILTER_FLAG_STRIP_LOW);
		$user = filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING);

		// initial login, set username and password
		if (!$password) {
			$crypted = password_hash($pw, PASSWORD_BCRYPT);
			$file = '<?php' . "\n";
			$file .= '$username = ' . "'" . $user . "';\n";
			$file .= '$password = ' . "'" . $crypted . "';\n";
			$file .= '?>' . "\n";
			file_put_contents('password.php', $file);

			// Login
			$_SESSION['login'] = $crypted;
			header('Location: index.php');
		}			
		else {
			// Login successful
			if ($user == $username && password_verify($pw, $password)) {
				$_SESSION['login'] = $password;
				header('Location: index.php');
			}
			else {
				session_destroy();
				header("HTTP/1.0 401 Unauthorized");
				header('Location: login.php?error=401');
			}
		}
	}
?>
