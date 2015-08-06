<?php
/*
	blogless - a blogless writing system
	Author:	 Martin Doering <martin@datenbrei.de>
	Project: http://blogless.datenbrei.de
	License: http://blogless.datenbrei.de/license.html
*/

	require_once('config.php');

	// allow_url_fopen must be set in php.ini!!!
	function do_ping($service, $source, $target) {
		$xml = xmlrpc_encode_request("pingback.ping", array($source, $target));
		//file_put_contents('ping-service.txt', $service);
		//file_put_contents('ping-xml.txt', $xml);
		
		$c = curl_init($service);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
		//curl_setopt($c, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($c, CURLOPT_TIMEOUT, 8);
		curl_setopt($c, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		//curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1); // allow redirects
		curl_setopt($c, CURLOPT_HEADER, true);
		curl_setopt($c, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
		curl_setopt($c, CURLOPT_POSTFIELDS, $xml);
		curl_setopt($c, CURLOPT_POST, 1);
		$data = curl_exec($c);

		$response = xmlrpc_decode($data);
		//file_put_contents('ping-response.txt', $data);
		if ($response && xmlrpc_is_fault($response)) 
			trigger_error("xmlrpc: $response[faultString] ($response[faultCode])");
	}

	# $article_text will be the full text of YOUR post
	# $article_url will be the full url of YOUR posting
	function send_ping($article_text, $article_url) {
		global $config;
		$regex = '#<a\s[^>]*href="([^"]+)"#i';
		preg_match_all($regex, $article_text, $matches);
		foreach ($matches[1] as $link) {
			if (preg_match('/^https{0,1}:/i', $link) && strpos($link, $config['baseurl']) === false) {
				// We found an external link!
				$c = curl_init();
				curl_setopt($c, CURLOPT_URL, $link);
				curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
				//curl_setopt($c, CURLOPT_HEADER, false);
				$page_data = curl_exec($c);
				curl_close($c);

				// See if service link contents pingback header (i have to unify this, but it works for now)
				if (preg_match('#<link rel="pingback" href="([^"]+)">#i', $page_data, $match))	{
					$service = $match[1];
					do_ping($service, $article_url, $link);
				}
				elseif (preg_match('#<link rel="pingback" href="([^"]+)" />#i', $page_data, $match))	{
					$service = $match[1];
					do_ping($service, $article_url, $link);
				}

				$i++;
			}
		}
	}
?>
