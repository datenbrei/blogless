<?php
/*
	blogless - a blogless writing system
	Author:  Martin Doering <martin@datenbrei.de>
	Project: http://blogless.datenbrei.de
	License: http://blogless.datenbrei.de/license.html
*/

	require('include.php');
	require('config.php');
	require('auth.php');
	
	function theme_chooser() {
		$html = '<select name="theme">' . "\n";
		$themes = get_theme_list();
		foreach ($themes as $theme) {
			$html .= '<option value="' . $theme . '" selected>' . ucfirst($theme) . '</option>';
		}
		$html .= '</select>' . "\n";
		return $html;
	}			

	if ($_SERVER["REQUEST_METHOD"] == "GET") {
		$html = "<!DOCTYPE html> \n";
		$html .= "<html> \n";
		$html .= "<head> \n";
		$html .= "<title>Settings</title> \n";
		$html .= "<meta charset=UTF-8> \n";
		$html .= '<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">' . "\n";
		$html .= '<link rel="stylesheet" href="admin.css" type="text/css" media="all">' . "\n";
		$html .= "</head> \n";
		$html .= "<body> \n";
		$html .= "<main> \n";
		$html .= '<h1>Settings</h1><br>' . "\n";

		if ($config['baseurl'] == '') 
			$config['baseurl'] = ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/';
		$html .= '<form method="post" action="settings.php" autocomplete="off">' . "\n";
		$html .= '<h2>Site Configuration</h2>' . "\n";
		$html .= '<p><label for="sitename">Site Name:</label><input type="text" name="sitename"  placeholder="The Name of your Website" value="' . $config['sitename'] . '"></p>' . "\n";
		$html .= '<p><label for="baseurl">Base URL:</label><input type="url" name="baseurl"  placeholder="The Base URL of your Domain" value="' . $config['baseurl'] . '"></p>' . "\n";
		$html .= '<p><label for="locale">Locale Code: </label><input type="text" name="locale" placeholder="Locale Code (en_US,de_DE,...)" value="' . $config['locale'] . '" required ></p>' . "\n";
		$html .= '<p><label for="dateformat">Date Format: </label><input type="text" name="dateformat" placeholder="e.g. for UK: %A, the %d%S of %B %Y (optional)" value="' . $config['dateformat'] . '" ></p>' . "\n";
		$html .= '<p><label for="footer">Footer (HTML): </label><input type="text" name="footer" placeholder="HTML code for the footer at the bottom of each page" value="' . htmlspecialchars(hex2bin($config['footer'])) . '"></p>' . "\n";
		$html .= '<p><label for="theme">Choose Theme: </label>' . theme_chooser() . '</p>';
		$html .= '<h2>Article Defaults</h2>' . "\n";
		$html .= '<p><label for="author">Author: </label><input type="text" name="author" placeholder="Author Name" value="' . $config['author'] . '" required ></p>' . "\n";
		$html .= '<p><label for="email">Author\'s Email: </label><input type="text" name="email" placeholder="Author\'s Email (will not be shown in the Public)" value="' . $config['email'] . '" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$"></p>' . "\n";
		$html .= '<p><label for="profile">Author\'s Profile: </label><input type="url" name="profile" placeholder="Author\'s Web Profile" value="' . $config['profile'] . '"></p>' . "\n";
		$html .= '<p><label for="twitter">Twitter: </label><input type="text" id="twitter" name="twitter" pattern="^@[A-Za-z0-9_]{1,15}$" placeholder="Your Twitter ID" value="' . $config['twitter'] . '" /></p>' . "\n";
		$html .= '<p><label for="facebook">Facebook: </label><input type="text" id="facebook" name="facebook" placeholder="Your Facebook profile URL" value="' . $config['facebook'] . '" /></p>' . "\n";
		$html .= '<p><label for="fbappid">Facebook App ID: </label><input type="text" id="fbappid" name="fbappid" placeholder="Your Facebook Application ID" value="' . $config['fbappid'] . '" /></p>' . "\n";
		$html .= '<p><label for="fbadmins">Facebook Admins: </label><input type="text" id="fbadmins" name="fbadmins" placeholder="Your Facebook Admin ID" value="' . $config['fbadmins'] . '" /></p>' . "\n";

		$html .= '<h2>Site Functionalities</h2>' . "\n";
		$flag = $config['rss'] == 'yes' ? 'checked' : '';
		$html .= '<p><label for="rss">RSS-Feed: </label><input type="checkbox" id="rss" name="rss" ' . $flag . '></p>' . "\n";
		$flag = $config['sitemap'] == 'yes' ? 'checked' : '';
		$html .= '<p><label for="sitemap">Sitemap: </label><input type="checkbox" id="sitemap" name="sitemap" ' . $flag . '></p>' . "\n";
		$flag = $config['articlelist'] == 'yes' ? 'checked' : '';
		$html .= '<p><label for="articlelist">Article List: </label><input type="checkbox" id="articlelist" name="articlelist" ' . $flag . '></p>' . "\n";
		$flag = $config['pingback'] == 'yes' ? 'checked' : '';
		$html .= '<p><label for="pingback">Enable Pingback: </label><input type="checkbox" id="pingback" name="pingback" ' . $flag . '></p>' . "\n";
		$html .= '<br>';
		$html .= '<input id="save" type="submit" value="Save">';

		
		$html .= "</form> \n";
		$html .= "</main> \n";
		$html .= "</body> \n";
		$html .= "</html>";
		header('Cache-Control: no-cache, must-revalidate');
		die($html);
	}
	elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
		$sitename = !empty($_POST['sitename']) ? $_POST['sitename'] : $_SERVER['HTTP_HOST'];
		$baseurl = !empty($_POST['baseurl']) ? $_POST['baseurl'] : ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/';
		$locale = !empty($_POST['locale']) ? $_POST['locale'] : 'en_US';
		$dateformat = !empty($_POST['dateformat']) ? $_POST['dateformat'] : '%A, the %d%S of %B %Y';
		$author = !empty($_POST['author']) ? $_POST['author'] : 'Anonymous';
		$email = !empty($_POST['email']) ? $_POST['email'] : '';
		$profile = !empty($_POST['profile']) ? $_POST['profile'] : '';
		$twitter = !empty($_POST['twitter']) ? $_POST['twitter'] : '';
		$facebook = !empty($_POST['facebook']) ? $_POST['facebook'] : '';
		$fbappid = !empty($_POST['fbappid']) ? $_POST['fbappid'] : '';
		$fbadmins = !empty($_POST['fbadmins']) ? $_POST['fbadmins'] : '';
		$rss = !empty($_POST['rss']) ? 'yes' : 'no';
		$sitemap = !empty($_POST['sitemap']) ? 'yes' : 'no';
		$articlelist = !empty($_POST['articlelist']) ? 'yes' : 'no';
		$pingback = !empty($_POST['pingback']) ? 'yes' : 'no';
		$footer = !empty($_POST['footer']) ? $_POST['footer'] : '';
		$theme = !empty($_POST['theme']) ? $_POST['theme'] : 'default';
		
		//Fix inconvenient input
		if (substr($baseurl, -1) != '/')
			$baseurl .= '/';
			
		$file = '<?php' . "\n";
		$file .= '$config = [];' . "\n";
		$file .= '$config["sitename"] = ' . "'" . $sitename . "';\n";
		$file .= '$config["baseurl"] = ' . "'" . $baseurl . "';\n";
		$file .= '$config["locale"] = ' . "'" . $locale . "';\n";
		$file .= '$config["language"] = ' . "'" . substr($locale, 0, 2) . "';\n";
		$file .= '$config["dateformat"] = ' . "'" . $dateformat . "';\n";
		$file .= '$config["author"] = ' . "'" . $author . "';\n";
		$file .= '$config["email"] = ' . "'" . $email . "';\n";
		$file .= '$config["profile"] = ' . "'" . $profile . "';\n";
		$file .= '$config["twitter"] = ' . "'" . $twitter . "';\n";
		$file .= '$config["facebook"] = ' . "'" . $facebook . "';\n";
		$file .= '$config["fbappid"] = ' . "'" . $fbappid . "';\n";
		$file .= '$config["fbadmins"] = ' . "'" . $fbadmins . "';\n";
		$file .= '$config["rss"] = ' . "'" . $rss . "';\n";
		$file .= '$config["sitemap"] = ' . "'" . $sitemap . "';\n";
		$file .= '$config["articlelist"] = ' . "'" . $articlelist . "';\n";
		$file .= '$config["pingback"] = ' . "'" . $pingback . "';\n";
		$file .= '$config["footer"] = ' . "'" . bin2hex($footer) . "';\n";
		$file .= '$config["theme"] = ' . "'" . $theme . "';\n";
		$file .= '?>' . "\n";
		file_put_contents('config.php', $file);
		
		if ($sitemap == 'yes')
			update_sitemap();
		else
			@unlink('../sitemap.xml');
		
		if ($rss == 'yes')
			update_rss();
		else
			@unlink('../feed.xml');

		header('Location: index.php');
	}
	
?>
