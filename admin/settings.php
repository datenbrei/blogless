<?php
/*
	blogless - a blogless writing system
	Author:  Martin Doering <martin@datenbrei.de>
	Project: http://blogless.datenbrei.de
	License: http://blogless.datenbrei.de/license/
*/

	require('include.php');
	require('config.php');
	require('auth.php');
	
	function theme_chooser($current) {
		$html = '<select name="theme">' . "\n";
		$themes = get_theme_list();
		foreach ($themes as $theme) {
			$html .= '<option value="' . $theme . '"' . ($theme === $current ? ' selected' : '') . '>' . ucfirst($theme) . '</option>';
		}
		$html .= '</select>' . "\n";
		return $html;
	}			
	
	$default_article_dir = dirname(getcwd()) . DIRECTORY_SEPARATOR .  'articles';

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
		$html .= '<p><label for="sitename">Site Name:</label><input type="text" name="sitename"  placeholder="The Name of your Website" value="' . $config['sitename'] . '" required></p>' . "\n";

		$html .= '<p><label for="locale">Locale Code: </label><input type="text" name="locale" placeholder="Locale Code (en_US,de_DE,...)" value="' . $config['locale'] . '" required ></p>' . "\n";
		$html .= '<p><label for="dateformat">Date Format: </label><input type="text" name="dateformat" placeholder="e.g. for UK: %A, the %d%S of %B %Y (optional)" value="' . $config['dateformat'] . '" ></p>' . "\n";
		$html .= '<p><label for="header">Header (HTML): </label><input type="text" name="header" placeholder="Optional HTML code for the top header/navbar on each page (default empty)" value="' . htmlspecialchars(hex2bin($config['header'])) . '"></p>' . "\n";
		$html .= '<p><label for="footer">Footer (HTML): </label><input type="text" name="footer" placeholder="Optional HTML code for the footer at the bottom of each page (default Sitename)" value="' . htmlspecialchars(hex2bin($config['footer'])) . '"></p>' . "\n";
		$html .= '<p><label for="theme">Choose Theme: </label>' . theme_chooser($config['theme']) . '</p>';
		$html .= '<p><label for="disqus">Disqus Name:</label><input type="text" name="disqus"  placeholder="Disqus Site Name for Commenting - setup on Disqus first" value="' . $config['disqus'] . '"></p>' . "\n";
		$html .= "<details> \n";
		$html .= "<summary><b>Server Directory Settings for Articles</b></summary> \n";
		$html .= '<p><label for="basedir">Attention!!!</label>The Base URL must point to the Article Directory - both must match!!!</p>' . "\n";
		$html .= '<p><label for="baseurl">Base URL:</label><input type="url" name="baseurl"  placeholder="The Base URL for your Articles (your Domain)" value="' . $config['baseurl'] . '" required></p>' . "\n";
		$html .= '<p><label for="basedir">Article Directory:</label><input type="text" name="basedir"  placeholder="The server\'s Article Directory (default ' . $default_article_dir . ')" value="' . $config['basedir'] . '"></p>' . "\n";
		$html .= "</details> \n";

		$html .= '<h2>Article Defaults</h2>' . "\n";
		$html .= '<p><label for="author">Author: </label><input type="text" name="author" placeholder="Author Name" value="' . $config['author'] . '" required></p>' . "\n";
		$html .= '<p><label for="email">Author\'s Email: </label><input type="text" name="email" placeholder="Author\'s Email (will not be shown in the Public)" value="' . $config['email'] . '" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$"></p>' . "\n";
		$html .= '<p><label for="profile">Author\'s Profile: </label><input type="url" name="profile" placeholder="Author\'s Web Profile" value="' . $config['profile'] . '"></p>' . "\n";
		$html .= '<p><label for="twitter">Twitter: </label><input type="text" id="twitter" name="twitter" pattern="^@[A-Za-z0-9_]{1,15}$" placeholder="Your Twitter ID" value="' . $config['twitter'] . '" /></p>' . "\n";
		$html .= '<p><label for="facebook">Facebook: </label><input type="text" id="facebook" name="facebook" placeholder="Your Facebook profile URL" value="' . $config['facebook'] . '" /></p>' . "\n";
		$html .= '<p><label for="fbappid">Facebook App ID: </label><input type="text" id="fbappid" name="fbappid" placeholder="Your Facebook Application ID" value="' . $config['fbappid'] . '" /></p>' . "\n";
		$html .= '<p><label for="fbadmins">Facebook Admins: </label><input type="text" id="fbadmins" name="fbadmins" placeholder="Your Facebook Admin ID" value="' . $config['fbadmins'] . '" /></p>' . "\n";

		$html .= '<h2>Site Options</h2>' . "\n";
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

		// Hidden field for old <-> new comparision
		$html .= '<input type="hidden" name="oldbasedir" value="' . $config['basedir'] . '">' . "\n";


		
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
		$basedir = !empty($_POST['basedir']) ? $_POST['basedir'] : $default_article_dir;
		$oldbasedir = !empty($_POST['oldbasedir']) ? $_POST['oldbasedir'] : $default_article_dir;
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
		$header = !empty($_POST['header']) ? $_POST['header'] : '';
		$footer = !empty($_POST['footer']) ? $_POST['footer'] : '';
		$theme = !empty($_POST['theme']) ? $_POST['theme'] : 'default';
		$disqus = !empty($_POST['disqus']) ? $_POST['disqus'] : '';
		
		//Fix inconvenient input
		if (substr($baseurl, -1) != '/')
			$baseurl .= '/';
			
		
		$file = '<?php' . "\n";
		$file .= '$config = [];' . "\n";
		$file .= '$config["sitename"] = ' . "'" . $sitename . "';\n";
		$file .= '$config["baseurl"] = ' . "'" . $baseurl . "';\n";
		$file .= '$config["basedir"] = ' . "'" . $basedir . "';\n";
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
		$file .= '$config["header"] = ' . "'" . bin2hex($header) . "';\n";
		$file .= '$config["footer"] = ' . "'" . bin2hex($footer) . "';\n";
		$file .= '$config["theme"] = ' . "'" . $theme . "';\n";
		$file .= '$config["disqus"] = ' . "'" . $disqus . "';\n";
		$file .= '?>' . "\n";
		file_put_contents('config.php', $file);

		// Move article directory, if changed
		if ($basedir != $oldbasedir) {
			rename($oldbasedir, $basedir);
		}
		
		// set the actual theme/stylesheets for whole website
		@unlink($config["basedir"] . DIRECTORY_SEPARATOR . 'stylesheet.css');
		@unlink($config["basedir"] . DIRECTORY_SEPARATOR . 'sitemap.css');
		copy('themes/' . $theme . '/stylesheet.css', $config["basedir"] . DIRECTORY_SEPARATOR . 'stylesheet.css');
		copy('themes/' . $theme . '/sitemap.css', $config["basedir"] . DIRECTORY_SEPARATOR . 'sitemap.css');
		
		// update or delete sitemap
		if ($sitemap == 'yes')
			update_sitemap();
		else
			@unlink($config["basedir"] . DIRECTORY_SEPARATOR . 'sitemap.xml');
		
		// update or delete RSS feed
		if ($rss == 'yes')
			update_rss();
		else
			@unlink($config["basedir"] . DIRECTORY_SEPARATOR . 'feed.xml');

		header('Location: index.php');
	}
	
?>
