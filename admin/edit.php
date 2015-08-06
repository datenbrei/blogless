<?php
/*
	blogless - a blogless writing system
	Author:  Martin Doering <martin@datenbrei.de>
	Project: http://blogless.datenbrei.de
	License: http://blogless.datenbrei.de/license.html
*/

	require('include.php');
	require_once('config.php');
	require('ping.php');
	
	// Check Login
	session_start();
	if (empty($_COOKIE['blogless']) or empty($_SESSION['login']) or $_COOKIE['blogless'] != $_SESSION['login']) {
		header('Location: login.php');
		die("Access denied");
	}
	
	
	function article_list() {
		$html = '<ul class="articlelist">' . "\n";
		$articles = get_article_list();
		foreach ($articles as $name) {
			if ($name == '040')
				continue;
			$article = get_article($name);
			$html .= '<li>';
			$html .= $article['created'];
			$html .= ' &#8212; ';
			$html .= '<a class="page" href="/' . $name . '/">' . $article['title'] . '</a>';
			$html .= '<p>' . $article['description'] . '</p>';
			$html .= '</li>' . "\n";
		}
		$html .= '</ul>' . "\n";
		return $html;
	}			

	function file_list($article, $path) {
		$html = '<ul class="articlelist">' . "\n";
		$files = get_file_list($path);
		foreach ($files as $file) {
			if ($file != 'index.html' && $file != 'feed.xml' && $file != 'sitemap.xml' && $file != 'style.css' )
				$html .= '<li><a class="page" href="delete.php?article=' . urlencode($article) . '&file=' . urlencode($file) . '" title="Delete">✖ </a>' . $file . "</li>\n";
		}
		$html .= '</ul>' . "\n";
		return $html;
	}			

	if ($_SERVER["REQUEST_METHOD"] == "GET") {
		$name = (isset($_GET['article'])) ? $_GET['article'] : NULL; 
		if ($name) {
			$article = get_article($name);
		}
		else {
			$name = date('Y-m-d', time());
			$article = get_article($name);
		}
		
		
		$html = "<!DOCTYPE html> \n";
		$html .= "<html> \n";
		$html .= "<head> \n";
		$html .= "<title>Edit Page</title> \n";
		$html .= "<meta charset=UTF-8> \n";
		$html .= '<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">' . "\n";
		$html .= '<link rel="stylesheet" href="admin.css" type="text/css" media="all">' . "\n";

		$html .= '<script type="text/javascript" src="tinymce/tinymce.min.js"></script>';
		$html .= '<script type="text/javascript">';
        $html .= 'tinymce.init({';
        $html .= 'selector: "#editor"';
		$html .= ', plugins: "link image charmap hr fullscreen emoticons code paste", image_dimensions: false';
		$html .= ', menubar: "false"';
        $html .= ', toolbar: "formatselect | bold italic underline strikethrough blockquote removeformat | bullist numlist hr | link unlink image charmap emoticons code | fullscreen"';
        $html .= '});';
		$html .= '</script>';

		$html .= "</head> \n";
		$html .= "<body> \n";
		$html .= "<main> \n";
		$html .= '<form method="post" action="edit.php" autocomplete="off">' . "\n";
		$html .= '<p><label for="title">Title:</label><input type="text" id="title" name="title"  placeholder="Title"  value="' . htmlspecialchars($article['title']) . '" autofocus required ></p>' . "\n";
		$html .= '<p><textarea id="editor" name="content" placeholder="Your Text" rows="10">' . htmlspecialchars($article['content']) . '</textarea></p>' . "\n";
		$html .= '<p><label for="author">Author:</label><input type="text" id="author" name="author" placeholder="Author Name" value="' . htmlspecialchars($article['author']) . '"></p>' . "\n";
		$html .= '<p><label for="description">Description:</label><input type="text" id="description" name="description" placeholder="Description"  value="' . htmlspecialchars($article['description']) . '" required></p>' . "\n";
		$html .= '<p><label for="language">Language Code</label><input type="text" id="language" name="language"  placeholder="Language, like en, de ir fr" value="' . htmlspecialchars($article['language']) . '" maxlength="2" required></p>' . "\n";
		$html .= "<details> \n";
		$html .= "<summary><b>Optional Article Settings</b></summary> \n";
		if ($name == 'index')
			$html .= '<p>index.html</p>' . "\n";
		else
			$html .= '<p><label for="name">Change Filename:</label><input type="text" id="name" name="name" placeholder="Filename" value="' . htmlspecialchars($name) . '" required></p>' . "\n";
		$html .= '<p><label for="created">Date Created:</label><input type="date" id="created" name="created" pattern="\d{4}-\d{2}-\d{2}" placeholder="Publishing Date in ISO-Format YYYY-MM-DD" value="' . htmlspecialchars($article['created']) . '" required ></p>' . "\n";
		$html .= '<p><label for="email">Author\'s Email: </label><input type="text" name="email" placeholder="Author\'s Email (will not be shown in the Public)" value="' . htmlspecialchars($config['email']) . '" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$"></p>' . "\n";
		$html .= '<p><label for="profile">Author\'s Profile:</label><input type="url" id="profile" name="profile" placeholder="Author\'s Web Profile (optional)" value="' . htmlspecialchars($article['profile']) . '"></p>' . "\n";
		$html .= '<p><label for="keywords">Keywords:</label><input type="text" id="keywords" name="keywords"  placeholder="Keywords (optional)" value="' . htmlspecialchars($article['keywords']) . '" ></p>' . "\n";
		$html .= '<p><label for="twitter">Twitter: </label><input type="text" id="twitter" name="twitter" pattern="^@[A-Za-z0-9_]{1,15}$" placeholder="Your Twitter ID" value="' . htmlspecialchars($article['twitter']) . '" /></p>' . "\n";
		$html .= "</details> \n";
		
		$html .= '<p><input id="save" type="submit" value="Save">';
		$html .= "</form> \n";
		
		if ($name == 'index')
			$dir = '../';
		else
			$dir = '../' . $name . '/';

		if (is_dir($dir)) {
			$html .= "<hr> \n";
			$html .= '<form enctype="multipart/form-data" action="upload.php" method="POST">' . "\n";
			//$html .= '<input type="hidden" name="MAX_FILE_SIZE" value="30000" />' . "\n";
			if ($name != 'index')
				$html .= '<input type="hidden" id="name" name="name" value="' . $name . '">' . "\n";
			$html .= '<p><label for="upload">Attached Files:</label><input id="upload" name="upload" type="file" /></p>' . "\n";
			$html .= '<input id="save" type="submit" value="Upload" />' . "\n";
			$html .= '</form>' . "\n";
			$html .= file_list($name, $dir);
			$html .= "<hr> \n";
		}
		$html .= "</main> \n";
		$html .= "</body> \n";
		$html .= "</html>";
		header('Cache-Control: no-cache, must-revalidate');
		die($html);
	}
	elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
		$dirname = !empty($_POST['name']) ? htmlspecialchars(strtolower($_POST['name'])) . '/' : '';
		$language = !empty($_POST['language']) ? htmlspecialchars($_POST['language']) : 'en';
		$title = !empty($_POST['title']) ? htmlspecialchars($_POST['title']) : 'untitled';
		$author = !empty($_POST['author']) ? htmlspecialchars($_POST['author']) : false;
		$email = !empty($_POST['email']) ? $_POST['email'] : '';
		$profile = !empty($_POST['profile']) ? $_POST['profile'] : false;
		$twitter = !empty($_POST['twitter']) ? $_POST['twitter'] : false;
		$created = !empty($_POST['created']) ? $_POST['created'] : date('Y-m-d', time());
		$description = !empty($_POST['description']) ? htmlspecialchars($_POST['description']) : 'For ' . $_POST['title'] . 'no description has been written yet';
		$keywords = !empty($_POST['keywords']) ? htmlspecialchars($_POST['keywords']) : '';
		$content = !empty($_POST['content']) ? $_POST['content'] : '';
		
		@setlocale(LC_ALL, $config['locale'] . '.UTF-8');

		$gravatar = 'http://www.gravatar.com/avatar/' . md5($email);
		$path = '../' . $dirname;
		$jpgpath = $path . 'article.jpg';
		$gifpath = $path . 'article.gif';
		$pngpath = $path . 'article.png';
		$svgpath = $path . 'article.svg';
		if (is_readable($jpgpath))
			$image = $config['baseurl'] . $dirname . 'article.jpg';
		elseif (is_readable($gifpath))
			$image = $config['baseurl'] . $dirname . 'article.gif';
		elseif (is_readable($pngpath))
			$image = $config['baseurl'] . $dirname . 'article.png';
		elseif (is_readable($svgpath))
			$image = $config['baseurl'] . $dirname . 'article.svg';
		elseif ($email != '')
			$image = 'http://www.gravatar.com/avatar/' . md5($email) . '.jpg?s=180';
		else
			$image = null;

		$html = '<!DOCTYPE html>' . "\n";
		$html .= '<html lang="' . $language . '" prefix="og: http://ogp.me/ns#">' . "\n";
		$html .= '<head>' . "\n";
		$html .= '<title>' . $title . '</title>' . "\n";
		$html .= "<meta charset=UTF-8> \n";
		$html .= '<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">' . "\n";
		if ($author)
			$html .= '<meta name="author" content="' . $author . '">' . "\n";
		$html .= '<meta name="created" content="' . $created . '">' . "\n";
		//$html .= '<meta http-equiv="last-modified" content="' . date('Y-m-d', time()) . '" />' . "\n";
		$html .= '<meta name="description" content="' . $description . '">' . "\n";
		$html .= '<meta name="keywords" content="' . $keywords . '">' . "\n";
		$html .= '<meta name="generator" content="blogless">' . "\n";
		$html .= '<meta name="gravatar" content="http://www.gravatar.com/avatar/' . md5($email) . '">' . "\n";
		if ($profile) 
			$html .= '<link rel="author" href="' . $profile . '">' . "\n";
		$html .= '<link rel="alternate" href="' . $config['baseurl'] . $dirname . '" hreflang="de">' . "\n";
		$html .= '<link rel="canonical" href="' . $config['baseurl'] . $dirname . '">' . "\n";

		// pingback server to be implemented
		//if ($config['pingback'] == 'yes')
		//	$html .= '<link rel="pingback" href="' . $config['baseurl'] . 'admin/pingback.php" >' . "\n";

		if ($dirname == '') { 
			$html .= '<link rel="stylesheet" href="admin/themes/' . $config['theme'] . '/stylesheet.css" type="text/css" media="all">' . "\n";
			$html .= '<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">' . "\n";
			$html .= '<link rel="icon" href="favicon.ico" type="image/x-icon">' . "\n";
		}
		else {
			$html .= '<link rel="stylesheet" href="../admin/themes/' . $config['theme'] . '/stylesheet.css" type="text/css" media="all">' . "\n";
			$html .= '<link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">' . "\n";
			$html .= '<link rel="icon" href="../favicon.ico" type="image/x-icon">' . "\n";
		}

		$html .= '<link rel="alternate" href="' . $config['baseurl'] . $dirname . '" hreflang="' . $language . '">' . "\n";
		$html .= '<link rel="canonical" href="' . $config['baseurl'] . $dirname . '">' . "\n";
		
		if ($config['rss'] == 'yes') 
			$html .= '<link rel="alternate" type="application/rss+xml" title="' . $_SERVER['HTTP_HOST'] . '" href="/feed.xml">' . "\n";
			
		$html .= '<meta property="og:type" content="article">' . "\n";
		$html .= '<meta property="og:title" content="' . $title . '">' . "\n";
		$html .= '<meta property="og:url" content="' . $config['baseurl'] . $dirname . '">' . "\n";
		$html .= '<meta property="og:description" content="' . $description . '">' . "\n";
		$html .= '<meta property="og:site_name" content="' . htmlspecialchars($config['sitename']) . '">' . "\n";
		if ($image) 
			$html .= '<meta property="og:image" content="' . $image . '">' . "\n";

		if ($twitter) { 
			$html .= '<meta name="twitter:card" content="summary" >' . "\n";
			$html .= '<meta name="twitter:site" content="' . $twitter . '" >' . "\n";
			$html .= '<meta name="twitter:title" content="' . $title . '" >' . "\n";
			$html .= '<meta name="twitter:description" content="' . $description . '" >' . "\n";
			$html .= '<meta name="twitter:url"  content="' . $config['baseurl'] . $dirname . '">' . "\n";
			if ($image)
				$html .= '<meta name="twitter:image" content="' . $image . '" >' . "\n";
		}		
		$html .= '</head>' . "\n";
		$html .= "\n";
		$html .= '<body>' . "\n";
		$html .= '<main>' . "\n";
		//$html .= '<header><h1><a href="' . $config['baseurl'] . '">' . $config['sitename'] . '</a></h1></header>' . "\n";
		$html .= '<article lang="' . $language . '" itemscope itemtype="http://schema.org/Article">' . "\n";
		$html .= '<header>' . "\n";
		
		$html .= '<div>' . "\n";
		$html .= '<h1 itemprop="headline">' . $title . '</h1>' . "\n";

		// author and time published
		if ($author) {
			if ($profile != '') 
				$html .= '<address><a href="' . $profile . '" itemprop="author" rel="author">' . $author . '</a></address> &#8212;' . "\n"; 
			else
				$html .= '<address itemprop="author">' . $author . '</address> &#8212;' . "\n"; 
		}
	
		if ($config['dateformat'] != '') 
			$html .= '<time itemprop="datePublished" datetime="' . $created . '">' . mystrftime($config['dateformat'], strtotime($created)) . '</time>' . "\n";	
		else
			$html .= '<time itemprop="datePublished" datetime="' . $created . '">' . strftime('%x', strtotime($created)) . '</time>' . "\n";	
		$html .= '<time itemprop="dateModified" datetime="' . date('Y-m-d', time()) . '"></time></div>' . "\n";	
		$html .= '</div>' . "\n";	

		// gravatar picture with link to author profile, if given
		if ($image) 
			if ($profile != '') 
				$html .= '<a href="' . $profile . '" itemprop="author" rel="author"><img id="gravatar" itemprop="image" src="' . $image . '" alt="Article Header"></a>' . "\n";
			else
				$html .= '<img id="gravatar" itemprop="image" src="' . $image . '" alt="Article Header">' . "\n";
		
		$html .= '</header>' . "\n";
		
		// article body
		$html .= '<div id="content" itemprop="articleBody">' . "\n";
		$html .= $content . "\n";
		$html .= '</div>' . "\n";
		$html .= '</article>' . "\n";
		$html .= '</main>' . "\n";
		
		//if this is the index page and PHP add the list of articles at the bottom
		if ($dirname == '' && $config['articlelist'] == 'yes') {
			$html .= '<hr>' . "\n";
			$html .= '<aside>' . "\n";
			$html .= article_list();
			$html .= '</aside>' . "\n";
		}

		$html .= '<hr>' . "\n";
		$html .= '<footer><a href="' . $config['baseurl'] . '">' . $config['sitename'] . '</a></footer>' . "\n";
		$html .= '</body>' . "\n";
		$html .= '</html>' . "\n";
	
		if ($dirname == '') {
			file_put_contents('../index.html', $html);
		}
		else {
			@mkdir ('../' . $dirname);
			file_put_contents('../' . $dirname . 'index.html', $html);
		}
		if ($config['sitemap'] == 'yes')
			update_sitemap();
		else
			@unlink('../sitemap.xml');
		
		if ($config['rss'] == 'yes')
			update_rss();
		else
			@unlink('../feed.xml');
		
		if ($config['pingback'] == 'yes') {
			send_ping($html, $config['baseurl'] . $dirname);
		}
		header('Location: index.php');
	}

?>
