<?php
require "functions.php";
require_once("lib/config.php");
require_once("lib/twitteroauth/twitteroauth.php");

if (!empty($_POST["twitter"])) {
{
	die('0');
}
else {
	// Establish connection with Twitter
	$connection = getConnectionWithAccessToken("","");
	//$connection->get('account/verify_credentials'); /*check the connection*/
	// Stop auto decoding JSON
	$connection->decode_json = FALSE;	
	// Add this user to friends list, first get their user ID
	$parameters = array('screen_name' => $_POST['twitter']);
	$method = "users/show";
	$userJSON = $connection->get($method, $parameters);
	$reply = json_decode($userJSON);
	if ($reply)
	{
		$id = $reply->id;		
		// Add a member to a list. Authenticated user must own list to be able to add members to it. Lists are limited to having 500 members.
		$parameters = array('id' => $id);
		$method = "$username/$list/members";
		$friends = $connection->post($method, $parameters);
		$reply = json_decode($friends);		
		if ($reply = json_decode($friends)
		{
			echo "User $id added to list<br />";
			echo 1;
		}
		else {
			// Do more error checking here
			echo "Failed adding ID $id to list<br />";
			die('0');
		}
	}
	else {
		// If there is no such user, return an error:
		echo "Given screen name is invalid. Please check your spelling.<br />";
		die('0');
	}
}
?>