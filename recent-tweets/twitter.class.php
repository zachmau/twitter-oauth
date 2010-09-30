<?php
class twitter {
	/*
		Get total number of followers.
	*/
	function followers_counter($username)
	{
		$cached_file = dirname(__FILE__) . '/cache/' . 'twitter-followers-counter-' . md5 ( $username );
		if (is_file ( $cached_file ) == false)
		{
			$cached_file_time = strtotime ( '1986-04-30 05:55' );
		}
		else {
			$cached_file_time = filemtime ( $cached_file );
		} 
		$now = strtotime ( date ( 'Y-m-d H:i:s' ) );
		$api_call = $cached_file_time;
		$difference = $now - $api_call;
		$api_time_seconds = 1800;
		if ($difference >= $api_time_seconds)
		{
			$api_page = 'http://twitter.com/users/show/' . $username;
			$xml = file_get_contents ( $api_page );
			$profile = new SimpleXMLElement ( $xml );
			$count = $profile->followers_count;
			if (is_file ( $cached_file ) == true)
			{
				unlink ( $cached_file );
			}
			touch ( $cached_file );
			file_put_contents ( $cached_file, strval ( $count ) );
			return strval ( $count );
		}
		else {
			$count = file_get_contents ( $cached_file );
			return strval ( $count );
		}
	}
	/*
		Format the date in relative time.
	*/
	function relativeTime($time)
	{
		define("SECOND", 1);
		define("MINUTE", 60 * SECOND);
		define("HOUR", 60 * MINUTE);
		define("DAY", 24 * HOUR);
		define("MONTH", 30 * DAY);
		$delta = strtotime('+2 hours') - $time;
		if ($delta < 2 * MINUTE)
		{
			return "1 min ago";
		}
		if ($delta < 45 * MINUTE)
		{
			return floor($delta / MINUTE) . " min ago";
		}
		if ($delta < 90 * MINUTE)
		{
			return "1 hour ago";
		}
		if ($delta < 24 * HOUR)
		{
			return floor($delta / HOUR) . " hours ago";
		}
		if ($delta < 48 * HOUR)
		{
			return "yesterday";
		}
		if ($delta < 30 * DAY)
		{
			return floor($delta / DAY) . " days ago";
		}
		if ($delta < 12 * MONTH)
		{
			$months = floor($delta / DAY / 30);
			return $months <= 1 ? "1 month ago" : $months . " months ago";
		}
		else {
			$years = floor($delta / DAY / 365);
			return $years <= 1 ? "1 year ago" : $years . " years ago";
		}
	}
	/*
		Get the latest twitter activity.
	*/
	function latest_tweet($username,$tweetnumber)
	{
		$username_for_feed = str_replace(" ", "+OR+from%3A", $username);
		$url = "http://search.twitter.com/search.atom?q=from%3A" . $username_for_feed . "&rpp=" . $tweetnumber * 2;
		$printed = 0;
		$username_for_file = str_replace(" ", "-", $username);
		$cache_file = dirname(__FILE__) . '/cache/' . $username_for_file . '-twitter-cache';
		$last = filemtime($cache_file);
		$now = time();
		$interval = 1800; // thirty minutes
		// check the cache file
		if ( !$last || (( $now - $last ) > $interval) ) 
		{
			// cache file doesn't exist or is too old, refresh it
			$cache_rss = file_get_contents($url);
			if (!$cache_rss)
			{
				print "\n";
				echo "<!-- ERROR: Twitter feed blank! Using cache file. -->";
				print "\n";
			}
			else {
				print "\n";
				echo "<!-- SUCCESS: Twitter feed used to update cache file. -->";
				print "\n";
				$cache_static = fopen($cache_file, 'wb');
				fwrite($cache_static, serialize($cache_rss));
				fclose($cache_static);
			}
			// read from the cache file
			$rss = @unserialize(file_get_contents($cache_file));
		}
		else {
			// cache file is was recently created, read from file
			print "\n";
			echo "<!-- SUCCESS: Cache file was recent enough to read from -->";
			print "\n";
			$rss = @unserialize(file_get_contents($cache_file));
		}
		// clean up and output the twitter feed
		$feed = str_replace("&amp;", "&", $rss);
		$feed = str_replace("&lt;", "<", $feed);
		$feed = str_replace("&gt;", ">", $feed);
		$clean = explode("<entry>", $feed);
		$clean = str_replace("&quot;", "'", $clean);
		$clean = str_replace("&apos;", "'", $clean);
		$amount = count($clean) - 1;
		if ($amount) {
			for ($i = 1; $i <= $amount; $i++) {
				if ($printed >= $tweetnumber) return;
				$entry_close = explode("</entry>", $clean[$i]);
				$clean_content_1 = explode("<content type=\"html\">", $entry_close[0]);
				$clean_content = explode("</content>", $clean_content_1[1]);
				$clean_name_2 = explode("<name>", $entry_close[0]);
				$clean_name_1 = explode("(", $clean_name_2[1]);
				$clean_name = explode(")</name>", $clean_name_1[1]);
				$clean_user = explode(" (", $clean_name_2[1]);
				$clean_lower_user = strtolower($clean_user[0]);
				$clean_uri_1 = explode("<uri>", $entry_close[0]);
				$clean_uri = explode("</uri>", $clean_uri_1[1]);
				$clean_time_1 = explode("<published>", $entry_close[0]);
				$clean_time = explode("</published>", $clean_time_1[1]);
				$unix_time = strtotime($clean_time[0]);
				$pretty_time = $this->relativeTime($unix_time);
				$tweet_title_1 = explode("<title>", $entry_close[0]);
				$tweet_title = explode("</title>", $tweet_title_1[1]);
				$first_char = substr($tweet_title[0][0], 0, 1);
				// if first tweet is not an @ reply, output it
				if ($first_char != "@")
				{
					$printed += 1;
					?>
					<li><p class="tweet"><?php echo $clean_content[0]; ?><br /><small><?php echo $pretty_time; ?></small>	</p></li>
					<?php
				}
				// else if first tweet is @ reply, skip it
				else {
					 echo "<!-- $clean_content[0]; -->";
					 print "\n";
				}
			}
		}
		// else if there are no tweets to display produce an error
		else {
			echo "<!-- ERROR: There are no tweets to display! -->";
		}
	}
}
?>