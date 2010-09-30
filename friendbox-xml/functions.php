<?php
function getConnectionWithAccessToken($oauth_token, $oauth_token_secret)
{
	// IF there is no consumer key OR consumer secret
	if (CONSUMER_KEY === '' || CONSUMER_SECRET === '')
	{
		echo 'You need a consumer key and secret. Get one from <a href="https://twitter.com/apps">https://twitter.com/apps</a>';
		exit;
	}
	else {
		$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $oauth_token, $oauth_token_secret);
		return $connection;
	}
}
function fetchElement($element,$src)
{
	$match = array();
	preg_match_all('/<'.$element.'>(.*)<\/'.$element.'>/u',$src,$match);
	// Matching the required property in the xml
	
	return $match[1];
	
	// ..and returning it
}
function error($msg)
{
	// format the error as a JSON object and exit the script
	die('{error:"'.$msg.'"}');
}
?>