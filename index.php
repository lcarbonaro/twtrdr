<?php
ini_set('display_errors', 1);
require_once('TwitterAPIExchange.php');

/** Set access tokens here - see: https://dev.twitter.com/apps/ **/
$settings = array(
    'oauth_access_token' => "***********************",
    'oauth_access_token_secret' => "****************",
    'consumer_key' => "***********************",
    'consumer_secret' => "********************"
);


/** Perform a GET request and echo the response **/
/** Note: Set the GET field BEFORE calling buildOauth(); **/
//$url = 'https://api.twitter.com/1.1/followers/ids.json';
$url = 'https://api.twitter.com/1.1/statuses/home_timeline.json';
$getfield = '?screen_name=lescarbonaro&count=50';
$requestMethod = 'GET';
$twitter = new TwitterAPIExchange($settings);
$respJson = $twitter->setGetfield($getfield)
             ->buildOauth($url, $requestMethod)
             ->performRequest();
             
$respArr = json_decode($respJson);             
$timeline = '';

//echo('<pre>timeline=<br/>');
//print_r($respArr);
//exit();

foreach ( $respArr as $tweetObj ) {
  $tweet = $tweetObj->created_at.'<br/>'.
           $tweetObj->user->screen_name.'<br/>'.
           $tweetObj->text;
           
  $targetUrl = 'https://twitter.com/lescarbonaro/statuses/'.$tweetObj->id;
           
  $link = '';
  if ( isset($tweetObj->entities->urls[0]) ) {  
    $link = '<a style="margin-left:4px;" target="_blank" href="'.
            $tweetObj->entities->urls[0]->url.'">[>>]</a>';
    $targetUrl = getFullUrl($tweetObj->entities->urls[0]->url);
  } else {
    if ( isset($tweetObj->retweeted_status) ) {
      $link = '<a style="margin-left:4px;" target="_blank" href="'.
              $tweetObj->retweeted_status->entities->urls[0]->url.'">[>>]</a>';
      $targetUrl = getFullUrl($tweetObj->retweeted_status->entities->urls[0]->url);
    }
  }
    
  $tweet .= $link.'<br/><div class="g-plusone" data-href="'.$targetUrl.'" data-annotation="none" data-size="small"></div><br/><hr/>';  
  $timeline .= $tweet;
}

function getFullUrl($shortUrl) {
    $ch = curl_init($shortUrl);
    curl_setopt($ch,CURLOPT_HEADER,true);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,false);
    $data = curl_exec($ch);
    $loc = '';
    if(preg_match('#Location: (.*)#', $data, $r)) {
        $loc = trim($r[1]);
    } else {
        if(preg_match('#location: (.*)#', $data, $r)) {
            $loc = trim($r[1]);
        }
    }
    return $loc;
}

?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
  </head>
  <body>
    <?php echo($timeline); ?>

<!-- Place this tag after the last +1 button tag. -->
<script type="text/javascript">
  (function() {
    var po = document.createElement("script"); po.type = "text/javascript"; po.async = true;
    po.src = "https://apis.google.com/js/plusone.js";
    var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(po, s);
  })();
</script>

  </body>
</html>
