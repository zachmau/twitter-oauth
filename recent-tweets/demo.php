<?php
ini_set('max_execution_time', 120);
require("twitter.class.php");

// create an instance of the twitter-class
$twitter = new twitter();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Recent Tweets Widget</title>
<link href="source/main.css" rel="stylesheet" type="text/css" media="screen" />
</head>

<body>
<!-- Display Single User Twitter Feed -->
<div id="twitter_box">
<ul id="twitter_update_list"><?php echo $twitter->latest_tweet('USERNAME-HERE', 5); ?></ul>
<div class="tell_followers"><a title="Follow @USERNAME-HERE" href="http://www.twitter.com/USERNAME-HERE" rel="external"><strong>@USERNAME-HERE</strong> <?php print $twitter->followers_counter('USERNAME-HERE') ?> followers</a></div>
</div>
</body>