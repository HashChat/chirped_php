<?php session_start();

echo "<title>Twt Chat " . ((isset($_GET['room']) && !empty($_GET['room']))? "- #" . $_GET['room'] : "")  . "</title>";

include 'lib/EpiCurl.php';
include 'lib/EpiOAuth.php';
include 'lib/EpiTwitter.php';
include 'lib/secret.php';

if( $_GET['action'] == 'signout' ){
    unset($_SESSION['ot']);
    unset($_SESSION['ots']);
    session_destroy();
}

$twitterObj = new EpiTwitter($consumer_key, $consumer_secret);
if(isset($_SESSION['ot']) && !empty($_SESSION['ot']))
$oauth_token = 'y';
else
$oauth_token = $_GET['oauth_token'];
	if($oauth_token == '')
  	  { 
	  	$url = $twitterObj->getAuthorizationUrl();
        
         if( isset($_GET['room']) )
            include 'tweets_listing.php';
        else
            include 'home.php';
     } 
	elseif( !isset($_SESSION['ot']) )
	  {
		$twitterObj->setToken($_GET['oauth_token']);
		$token = $twitterObj->getAccessToken();
		$twitterObj->setToken($token->oauth_token, $token->oauth_token_secret);	  	
		$_SESSION['ot'] = $token->oauth_token;
		$_SESSION['ots'] = $token->oauth_token_secret;
		$twitterInfo= $twitterObj->get_accountVerify_credentials();
		$twitterInfo->response;
		
		$username = $twitterInfo->screen_name;
		$profilepic = $twitterInfo->profile_image_url;
        
     } 

if(isset($_POST['message']))
	  {
	  	$msg = $_POST['message'] . ' #' . $_POST['room'];
		
		$twitterObj->setToken($_POST['ot'], $_POST['ots']);
		$update_status = $twitterObj->post_statusesUpdate(array('status' => $msg,'include_entities' => 1));
		$temp = $update_status->response;
		
        exit();
	  }
      
      if( isset($_SESSION['ot']) && isset($_SESSION['ots']) )
      {
        $twitterObj->setToken($_SESSION['ot'], $_SESSION['ots']);	  	
		$_SESSION['ot'] = $_SESSION['ot'];
		$_SESSION['ots'] = $_SESSION['ots'];
		$twitterInfo= $twitterObj->get_accountVerify_credentials();
        
		$twitterInfo->response;
		
		$username = $twitterInfo->screen_name;
		$name = $twitterInfo->name;
		$profilepic = $twitterInfo->profile_image_url;
          
          if( isset($_GET['room']) )
            include 'tweets_listing.php';
          else
            include 'home.php';
        }
?> 