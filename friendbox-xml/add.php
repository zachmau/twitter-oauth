<?php
require "functions.php";
require_once("lib/config.php");
require_once("lib/twitteroauth/twitteroauth.php");

if(!($_POST['twitter']))
{
	die('0');
}
else {

	$connection = getConnectionWithAccessToken("106653838-uwSJGOjQlgKQEzzVoJyl8TfrYtARniCbFp82eYH7","uTLLEGD1TuEGCChj8qzg6kObM9v6TKFILe8W6fGkd2k");
	$connection->get('account/verify_credentials');
	$connection->format = 'xml';
	
	//Add this user to friends list, first get their user ID using this format:
	$parameters = array('screen_name' => $_POST['twitter']); 
	$method = "users/show";
	$userXML = $connection->get($method, $parameters);
		
	if($connection->http_code == 404)
	{
		// If there is no such user, return an error:
		echo "no user found";
		die('0');
	}
	// fetchElement returns an array, and the list function assigns its first element to $id:
	list($id) = fetchElement('id',$userXML);
		
	// Add a member to a list. Authenticated user must own list to be able to add members to it. Lists are limited to having 500 members.
	$parameters = array('id' => $id);
	$method = "$username/$list/members";
	$connection->post($method, $parameters);
	
echo 1;
}
?>