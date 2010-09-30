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
	$connection = getConnectionWithAccessToken("","");
	// RETURN the members of the friends list
	$method = "$username/$list/members";
	$members = $connection->get($method);
	// IF there is not such a list, CREATE it automatically:
	if($connection->http_code == 404)
	{
		$method = "$username/lists";
		$parameters = array('name' => 'friends');
		$list = $connection->post($method, $parameters);
	}
	// Generate array with structure 0-screenname 1-avatar
	$users = array();
	for ($i=0; $i < count($members['users']); $i++)
	{
	   $users[$i]['screenname'] = $members['users'][$i]['screen_name'];
	   $users[$i]['avatar'] = $members['users'][$i]['profile_image_url'];
	}
	json_encode($users);
	// Generate JSON object with structure: screen_name:profile_image_url
	/*
	
	*/
	// GET the total number of friends who have been added to the list
	$method = "$username/lists/$list";
	$membersXML = $connection->get($method);
	$membersCount = $user->member_count;
	$json = array('members' => $json, 'membersCount' => $membersCount[0], 'fanPage' => "http://twitter.com/" . $username . "/" . $list );
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
echo json_encode($json);
?>