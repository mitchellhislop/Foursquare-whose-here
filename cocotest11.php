<html>
<head></head>
<body>
<p>Test for CoCo</p>
<?php

$account_links = array();
$images = array();

//URL encode the query string
$q = urlencode("CoCo coworking and collaborative");

//request URL
$request = "http://search.twitter.com/search.atom?q=$q&lang=en";

$curl= curl_init();

curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);

curl_setopt ($curl, CURLOPT_URL,$request);

$response = curl_exec ($curl);

curl_close($curl);

//remove "twitter:" from the $response string
$response = str_replace("twitter:", "", $response);

//convert response XML into an object
$xml = simplexml_load_string($response);

//wrapping the whole output with <result></result>
echo "<results>";

$account_links = array();
$images = array();

//loop through all the entry(s) in the feed
for($i=0;$i<count($xml->entry);$i++)
{

	//get the id from entry
	$id = $xml->entry[$i]->id;

	//explode the $id by ":"
	$id_parts = explode(":",$id);

	//the last part is the tweet id
	$tweet_id = array_pop($id_parts);

	//get the account link
	$account_link = $xml->entry[$i]->author->uri;

	//get the image link
	$image_link = $xml->entry[$i]->link[1]->attributes()->href;

	//get name from entry and trim the last ")"
	$name = trim($xml->entry[$i]->author->name, ")");

	//explode $name by the rest "(" inside it
	$name_parts = explode("(", $name);

	//get the real name of user from the last part
	$real_name = trim(array_pop($name_parts));

	//the rest part is the screen name
	$screen_name = trim(array_pop($name_parts));

	//get the published time, replace T and Z with " " and trim the last " "
	$published_time = trim(str_replace(array("T","Z")," ",$xml->entry[$i]->published));

	//get the status link
	$status_link = $xml->entry[$i]->link[0]->attributes()->href;

	//get the tweet
	$tweet = $xml->entry[$i]->content;

	//remove <b> and </b> from the tweet. If you want to show bold keyword then you can comment this line
	$tweet = str_replace(array("<b>", "</b>"), "", $tweet);

	//get the source link
	$source = $xml->entry[$i]->source;
	
	if(!in_array($image_link, $images))
	{
		$images[] = $image_link;
		$account_links[] = $account_link;
	}
}

foreach($images as $key => $image)
{
	//the result div that holds the information
	echo '<div class="profile_image"><a href="'. $account_links[$key] .'"><img src="'. $image .'"></a></div>';
}

echo "</results>";

?>
</body>
</html>