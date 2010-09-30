<?php
require "functions.php";
require_once("lib/config.php");
require_once("lib/twitteroauth/twitteroauth.php");
$cache_file = 'twitter.cache';
// expire the cache after 15 minutes
$cache_expire_time = 15*60;
$twitterers_shown = 12;
// to destroy cache while testing uncomment the line below
// $cache_expire_time = 1;

// if there isn't a cache file, or if it is older than allowed
if(!file_exists($cache_file) || time() - filemtime($cache_file) > $cache_expire_time)
{
	// CONNECT to twitter using twitteroauth
	$connection = getConnectionWithAccessToken("106653838-uwSJGOjQlgKQEzzVoJyl8TfrYtARniCbFp82eYH7","uTLLEGD1TuEGCChj8qzg6kObM9v6TKFILe8W6fGkd2k");
	$connection->get('account/verify_credentials');
	$connection->format = 'xml';
	// RETURN the members of the friends list
	$method = "$username/$list/members";
	$xml = $connection->get($method);
	// IF there is not such a list, CREATE it automatically:
	if(strpos($xml,'<error>Not found</error>') !== false)
	{
		$method = "$username/lists";
		$parameters = array('name' => 'friends');
		$list = $connection->post($method, $parameters);
	}
	$usernames = fetchElement('screen_name',$xml);
	$avatars = fetchElement('profile_image_url',$xml);
	$json = '';
	foreach($usernames as $k=>$u)
	{
		if($k!=0) $json.=', ';
		$json.='"'.$u.'":"'.$avatars[$k].'"';
		// Generating the json object with a structure: username:avatar_image
		
		if($k>=$twitterers_shown-1) break;
	}
	// GET the total number of friends who have been added to the list
	$method = "$username/lists/$list";
	$membersXML = $connection->get($method);
	$membersCount = fetchElement('member_count',$membersXML);
	$json = '{"members":{'.$json.'}, "membersCount":'.$membersCount[0].',"fanPage":"http://twitter.com/'.$username.'/'.$list.'"}';
	// save the generated json variable in the cache for later use:
	$fp = fopen($cache_file,'w');
	if($fp == false)
	{
		error("Cache file could not be created!");
	}
	fwrite($fp,$json);
	fclose($fp);
}
else
{
	// Fetch the data from the cache file
	$json = file_get_contents($cache_file);
}
echo $json;
?>