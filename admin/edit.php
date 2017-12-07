<?php
/*
	blogless - a blogless writing system
	Author:  Martin Doering <martin@datenbrei.de>
	Project: http://blogless.datenbrei.de
	License: http://blogless.datenbrei.de/license/
*/

	require('include.php');
	require_once('config.php');
	require('ping.php');
	require('auth.php');
		
	// Add the meta part to the html header
	function add_meta($config, $author, $created, $description, $keywords, $email, $title, $dirname, $image, $twitter, $facebook, $fbappid, $fbadmins, $draft, $disqus) {

		// meta names
		$html = '<meta name="created" content="' . $created . '">' . "\n";
		$html .= '<meta name="description" content="' . $description . '">' . "\n";
		$html .= '<meta name="keywords" content="' . $keywords . '">' . "\n";
		$html .= '<meta name="generator" content="blogless">' . "\n";
		if ($author)
			$html .= '<meta name="author" content="' . $author . '">' . "\n";
		if ($draft)
			$html .= '<meta name="draft" content="yes">' . "\n";
		if ($email)
			$html .= '<meta name="gravatar" content="http://www.gravatar.com/avatar/' . md5($email) . '">' . "\n";

		if ($twitter) { 
			$html .= '<meta name="twitter:card" content="summary" >' . "\n";
			$html .= '<meta name="twitter:site" content="' . $twitter . '" >' . "\n";
			$html .= '<meta name="twitter:title" content="' . $title . '" >' . "\n";
			$html .= '<meta name="twitter:description" content="' . $description . '" >' . "\n";
			$html .= '<meta name="twitter:url"  content="' . $config['baseurl'] . $dirname . '">' . "\n";
			if ($image)
				$html .= '<meta name="twitter:image" content="' . $image . '" >' . "\n";
		}		

		// meta properties
		$html .= '<meta property="og:type" content="article">' . "\n";
		$html .= '<meta property="og:title" content="' . $title . '">' . "\n";
		$html .= '<meta property="og:url" content="' . $config['baseurl'] . $dirname . '">' . "\n";
		$html .= '<meta property="og:description" content="' . $description . '">' . "\n";
		$html .= '<meta property="og:site_name" content="' . htmlspecialchars($config['sitename']) . '">' . "\n";
		if ($image) 
			$html .= '<meta property="og:image" content="' . $image . '">' . "\n";

		if ($facebook) { 
			$html .= '<meta property="article:author" content="' . $facebook . '" >' . "\n";
		}		

		if ($fbappid) { 
			$html .= '<meta property="fb:app_id" content="' . $fbappid . '" >' . "\n";
		}		

		if ($fbadmins) { 
			$html .= '<meta property="fb:admins" content="' . $fbadmins . '" >' . "\n";
		}		

		if ($disqus) { 
			$html .= '<meta name="disqus" content="' . $config['disqus'] . '" >' . "\n";
		}		

		return $html;
	}
	
	// Add the link part to the html header, stylesheet, icons, ...
	function add_link ($config, $profile, $dirname, $language) {
		$html = '';
		if ($profile) 
			$html .= '<link rel="author" href="' . $profile . '">' . "\n";

		// pingback server to be implemented
		//if ($config['pingback'] == 'yes')
		//	$html .= '<link rel="pingback" href="' . $config['baseurl'] . 'admin/pingback.php" >' . "\n";

		if (!$dirname) { 
			$html .= '<link rel="stylesheet" href="stylesheet.css" type="text/css" media="all">' . "\n";
			$html .= '<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">' . "\n";
			$html .= '<link rel="icon" href="favicon.ico" type="image/x-icon">' . "\n";
		}
		else {
			$html .= '<link rel="stylesheet" href="../stylesheet.css" type="text/css" media="all">' . "\n";
			$html .= '<link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">' . "\n";
			$html .= '<link rel="icon" href="../favicon.ico" type="image/x-icon">' . "\n";
		}

		$html .= '<link rel="alternate" href="' . $config['baseurl'] . $dirname . '" hreflang="' . $language . '">' . "\n";
		$html .= '<link rel="canonical" href="' . $config['baseurl'] . $dirname . '">' . "\n";
		
		if ($config['rss'] == 'yes') 
			$html .= '<link rel="alternate" type="application/rss+xml" title="' . $_SERVER['HTTP_HOST'] . '" href="/feed.xml">' . "\n";
			
		return $html;
	}
	
	// Add the link part to the html header, stylesheet, icons, ...
	function add_disqus ($config, $dirname) {
		$html = "<aside> \n";
		$html .= '<div id="disqus_thread"></div>' . "\n";
		$html .= '<script>' . "\n";
		$html .= 'var disqus_config = function () {' . "\n";
		$html .= "this.page.url = '" . $config['baseurl'] . $dirname . "';\n";
		$html .= "this.page.identifier = '" . rtrim($dirname, '/') . "';\n";
		$html .= '};' . "\n";
		$html .= '(function() {' . "\n";
		$html .= "var d = document, s = d.createElement('script');" . "\n";
		$html .= "s.src = '//" . $config['disqus'] . ".disqus.com/embed.js';" . "\n";
		$html .= "s.setAttribute('data-timestamp', +new Date());" . "\n";
		$html .= '(d.head || d.body).appendChild(s);' . "\n";
		$html .= '})();' . "\n";
		$html .= '</script>' . "\n";
		$html .= '<noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript" rel="nofollow">comments powered by Disqus.</a></noscript>' . "\n";
		$html .= "</aside> \n";
		$html .= '<hr>' . "\n";
		
		return $html;
	}
	
	// See, if we can find an image for our article, fallback to gravatar, if possible
	function find_image($config, $dirname, $email) {
		$path = $config["basedir"] . DIRECTORY_SEPARATOR . $dirname;
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
		elseif ($email)
			$image = 'http://www.gravatar.com/avatar/' . md5($email) . '.jpg?s=180';
		else
			$image = false;

		return $image;
	}
	
	// Add the article header with title, author, pubishing date, ...
	function add_heading($config, $title, $author, $profile, $created, $gravatar) {
		$html = '<header>' . "\n";
		$html .= '<div>' . "\n";
		$html .= '<h1 itemprop="headline">' . $title . '</h1>' . "\n";

		// author
		if ($author) {
			if ($profile != '') 
				$html .= '<address><a href="' . $profile . '" itemprop="author" rel="author">' . $author . '</a></address> &#8212;' . "\n"; 
			else
				$html .= '<address itemprop="author">' . $author . '</address> &#8212;' . "\n"; 
		}

		// time 
		if ($config['dateformat'] != '') 
			$html .= '<time itemprop="datePublished" datetime="' . $created . '">' . mystrftime($config['dateformat'], strtotime($created)) . '</time>' . "\n";	
		else
			$html .= '<time itemprop="datePublished" datetime="' . $created . '">' . strftime('%x', strtotime($created)) . '</time>' . "\n";	
		$html .= '<time itemprop="dateModified" datetime="' . date('Y-m-d', time()) . '"></time>' . "\n";	
		$html .= '</div>' . "\n";	

		// gravatar picture with link to author profile, if given
		if ($gravatar) 
			if ($profile != '') 
				$html .= '<a href="' . $profile . '" itemprop="author" rel="author"><img id="gravatar" itemprop="image" src="' . $gravatar . '" title="Author" alt="Author"></a>' . "\n";
			else
				$html .= '<img id="gravatar" itemprop="image" src="' . $gravatar . '" title="Author" alt="Author">' . "\n";
		$html .= '</header>' . "\n";

		return $html;
	}	

	// Add the article content to the html output
	function add_content($content) {
		// article body
		$html = '<div id="content" itemprop="articleBody">' . "\n";
		$html .= $content . "\n";
		$html .= '</div>' . "\n";
		
		return $html;
	}

	// Add an article list to the homepage, if configured
	function add_article_list($config, $dirname) {

		$html = '';
		if (!$dirname && $config['articlelist'] == 'yes') {
			$html .= '<hr>' . "\n";
			$html .= '<aside role="complementary">' . "\n";
			$html .= '<ul class="articlelist">' . "\n";
			$articles = get_article_list();
			foreach ($articles as $name) {
				if ($name == '040')
					continue;
				$article = get_article($name);
				if ($article['draft'])
					continue;
				$html .= '<li>';
				$html .= htmlspecialchars($article['created']);
				$html .= ' &#8212; ';
				$html .= '<a class="page" href="/' . htmlspecialchars($name) . '/" title="' . htmlspecialchars($article['description']) . '">' . htmlspecialchars($article['title']) . '</a>';
				$html .= '<p>' . $article['description'] . '</p>';
				$html .= '</li>' . "\n";
			}
			$html .= '</ul>' . "\n";
			$html .= '</aside>' . "\n";
		}

		return $html;
	}			

	// Gives back a list of optional files provided with the article
	function file_list($article, $path) {
		$html = '<ul class="articlelist">' . "\n";
		$files = get_file_list($path);
		foreach ($files as $file) {
			if ($file != 'index.html' && $file != 'feed.xml' && $file != 'sitemap.xml' && $file != 'style.css' ) {
				if ($article)
					$html .= '<li><a class="page" href="delete.php?article=' . urlencode($article) . '&file=' . urlencode($file) . '" title="Delete">✖ </a>' . $file . "</li>\n";
				else
					$html .= '<li><a class="page" href="delete.php?file=' . urlencode($file) . '" title="Delete">✖ </a>' . $file . "</li>\n";
			}
		}
		$html .= '</ul>' . "\n";
		return $html;
	}			
	
	// Write article to disk, ping remote hosts and update sitemap and rss
	function save_article ($config, $dirname, $created, $html) {
		if ($dirname)
			@mkdir ($config["basedir"] . DIRECTORY_SEPARATOR . $dirname);
		file_put_contents($config["basedir"] . DIRECTORY_SEPARATOR . $dirname . 'index.html', $html);
		@touch($config["basedir"] . DIRECTORY_SEPARATOR . $dirname, strtotime($created));
		touch($config["basedir"] . DIRECTORY_SEPARATOR . $dirname . 'index.html', strtotime($created));

		if ($config['sitemap'] == 'yes')
			update_sitemap();
		else
			@unlink($config["basedir"] . DIRECTORY_SEPARATOR . 'sitemap.xml');

		if ($config['rss'] == 'yes')
			update_rss();
		else
			@unlink($config["basedir"] . DIRECTORY_SEPARATOR . 'feed.xml');
		
		if ($config['pingback'] == 'yes') {
			send_ping($html, $config['baseurl'] . $dirname);
		}
	}

	function remove_image_size($text) {
		// replace images
		$pattern = "#<img([^>]*) width=\"([^\"]*)\" height=\"([^\"]*)\"#";
		$replace = "<img\${1}";
		$text = preg_replace($pattern, $replace, $text);
		
		return $text;
	}


	// Process GET and POST requests
	if ($_SERVER["REQUEST_METHOD"] == "GET") {
		$name = (isset($_GET['article'])) ? $_GET['article'] : NULL; 
		$article = get_article($name);

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
    $html .= 'selector: "#editor", ';
		$html .= 'plugins: "link image charmap hr fullscreen emoticons code paste", ';
		$html .= 'document_base_url: "' . $article['url'] . '", ';
		$html .= 'image_dimensions: true, ';
		$html .= 'object_resizing : false, ';
		$html .= 'menubar: "false", ';
    $html .= 'toolbar: "formatselect | bold italic underline strikethrough blockquote removeformat | bullist numlist hr | link unlink image charmap emoticons code | fullscreen"';
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
		$html .= '<p><label for="language">Language Code</label><input type="text" id="language" name="language"  placeholder="Language, like en, de or fr" value="' . htmlspecialchars($article['language']) . '" maxlength="2" required></p>' . "\n";
		$flag = $article['draft'] == true ? 'checked' : '';
		$html .= '<p><label for="draft">Draft: </label><input type="checkbox" id="draft" title="Article will be excluded from rss, sitemap and article list!" name="draft" ' . $flag . '>' . "</p>\n";
		$html .= "<details> \n";
		$html .= "<summary><b>Optional Article Settings</b></summary> \n";
		if ($name) {
			$html .= '<p><label for="name">Change Filename:</label><input type="text" id="name" name="name" placeholder="Filename" value="' . htmlspecialchars($name) . '" required></p>' . "\n";
			$html .= '<input type="hidden" name="origname" value="' . htmlspecialchars($name) . '">' . "\n";
		}
		else
			$html .= '<p>index.html</p>' . "\n";
		$html .= '<p><label for="created">Date Created:</label><input type="date" id="created" name="created" pattern="\d{4}-\d{2}-\d{2}" placeholder="Publishing Date in ISO-Format YYYY-MM-DD" value="' . htmlspecialchars($article['created']) . '" required ></p>' . "\n";
		$html .= '<p><label for="email">Author\'s Email: </label><input type="text" name="email" placeholder="Author\'s Email (will not be shown in the Public)" value="' . htmlspecialchars($config['email']) . '" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$"></p>' . "\n";
		$html .= '<p><label for="profile">Author\'s Profile:</label><input type="url" id="profile" name="profile" placeholder="Author\'s Web Profile (optional)" value="' . htmlspecialchars($article['profile']) . '"></p>' . "\n";
		$html .= '<p><label for="keywords">Keywords:</label><input type="text" id="keywords" name="keywords"  placeholder="Keywords (optional)" value="' . htmlspecialchars($article['keywords']) . '" ></p>' . "\n";
		$html .= '<p><label for="twitter">Twitter: </label><input type="text" id="twitter" name="twitter" pattern="^@[A-Za-z0-9_]{1,15}$" placeholder="Your Twitter ID" value="' . htmlspecialchars($article['twitter']) . '" /></p>' . "\n";
		$html .= '<p><label for="facebook">Facebook: </label><input type="text" id="facebook" name="facebook" placeholder="Your Facebook profile URL" value="' . htmlspecialchars($article['facebook']) . '" /></p>' . "\n";
		$flag = $article['disqus'] != '' ? 'checked' : '';
		$html .= '<p><label for="disqus">Comments: </label><input type="checkbox" id="disqus" title="Use Disqus Comments for this Article." name="disqus" ' . $flag . '>' . "</p>\n";
		$html .= "</details> \n";
		
		$html .= '<p><input id="save" type="submit" value="Save">';
		$html .= "</form> \n";
		
		if ($name)
			$dir = $config["basedir"] . DIRECTORY_SEPARATOR . $name . '/';
		else
			$dir = $config["basedir"] . DIRECTORY_SEPARATOR;

		if (is_dir($dir)) {
			$html .= "<hr> \n";
			$html .= '<form enctype="multipart/form-data" action="upload.php" method="POST">' . "\n";
			//$html .= '<input type="hidden" name="MAX_FILE_SIZE" value="30000" />' . "\n";
			if ($name)
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
		@setlocale(LC_ALL, $config['locale'] . '.UTF-8');

		$dirname = !empty($_POST['name']) ? htmlspecialchars(strtolower($_POST['name'])) . '/' : false;
		$origname = !empty($_POST['origname']) ? htmlspecialchars(strtolower($_POST['origname'])) . '/' : false;
		// can't use empty article filename
		if ($origname && !$dirname)
			$dirname = $origname;

		$language = !empty($_POST['language']) ? htmlspecialchars($_POST['language']) : 'en';
		$title = !empty($_POST['title']) ? htmlspecialchars($_POST['title']) : 'untitled';
		$author = !empty($_POST['author']) ? htmlspecialchars($_POST['author']) : false;
		$email = !empty($_POST['email']) ? $_POST['email'] : false;
		$profile = !empty($_POST['profile']) ? $_POST['profile'] : false;
		$twitter = !empty($_POST['twitter']) ? $_POST['twitter'] : false;
		$facebook = !empty($_POST['facebook']) ? $_POST['facebook'] : false;
		$created = !empty($_POST['created']) ? $_POST['created'] : date('Y-m-d', time());
		$description = !empty($_POST['description']) ? htmlspecialchars($_POST['description']) : 'For ' . $_POST['title'] . 'no description has been written yet';
		$keywords = !empty($_POST['keywords']) ? htmlspecialchars($_POST['keywords']) : false;
		$content = !empty($_POST['content']) ? $_POST['content'] : false;
		$draft = !empty($_POST['draft']) ? true : false;
		$disqus = !empty($_POST['disqus']) && $config['disqus'] ? true : false;
		
		// remove image sizing from content
		//<img src="gravatar.jpeg" alt="eee" width="232" height="232" />
		$content = remove_image_size($content);

		// just get fresh from config each time and don't store in article
		$fbappid = !empty($config['fbappid']) ? $config['fbappid'] : false;
		$fbadmins = !empty($config['fbadmins']) ? $config['fbadmins'] : false;
		$header = !empty($config['header']) ? hex2bin($config['header']) : false;
		$footer = !empty($config['footer']) ? hex2bin($config['footer']) : false;

		$gravatar = $email ? 'http://www.gravatar.com/avatar/' . md5($email) : false;
		$image = find_image($config, $dirname, $email);

		$html = '<!DOCTYPE html>' . "\n";
		$html .= '<html lang="' . $language . '" prefix="og: http://ogp.me/ns#">' . "\n";
		$html .= '<head>' . "\n";
		$html .= '<title>' . $title . '</title>' . "\n";
		$html .= "<meta charset=UTF-8> \n";
		$html .= '<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">' . "\n";
		$html .= add_meta($config, $author, $created, $description, $keywords, $email, $title, $dirname, $image, $twitter, $facebook, $fbappid, $fbadmins, $draft, $disqus);
		$html .= add_link ($config, $profile, $dirname, $language);
		$html .= '</head>' . "\n";
		$html .= "\n";
		$html .= '<body>' . "\n";
		$html .= $header . "\n";
		$html .= '<article role="main" lang="' . $language . '" itemscope itemtype="http://schema.org/Article">' . "\n";
		$html .= add_heading($config, $title, $author, $profile, $created, $gravatar);
		$html .= add_content($content);
		$html .= '</article>' . "\n";
		$html .= add_article_list($config, $dirname);
		$html .= '<hr>' . "\n";

		if ($disqus)
			$html .= add_disqus ($config, $dirname);

		if ($footer)
			$html .= '<footer>' . $footer . '</footer>' . "\n";
		else
			$html .= '<footer><a href="' . $config['baseurl'] . '">' . $config['sitename'] . '</a></footer>' . "\n";
			
		$html .= '</body>' . "\n";
		$html .= '</html>' . "\n";
		
		// rename article and move folder before updating content
		if ($dirname && $origname && $origname != $dirname)
			rename($config["basedir"] . DIRECTORY_SEPARATOR . $origname, $config["basedir"] . DIRECTORY_SEPARATOR . $dirname);
		
		save_article ($config, $dirname, $created, $html);
		header('Location: index.php');
	}

?>
