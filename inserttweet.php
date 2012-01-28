<?php
include 'lib/EpiCurl.php';
include 'lib/EpiOAuth.php';
include 'lib/EpiTwitter.php';
include 'lib/secret.php';

$twitterObj = new EpiTwitter($consumer_key, $consumer_secret);

if(isset($_POST['message']))
	  {
	  	$msg = $_POST['message'] . ' #' . $_POST['room'];
		
		$twitterObj->setToken($_POST['ot'], $_POST['ots']);
		$update_status = $twitterObj->post_statusesUpdate(array('status' => $msg,'include_entities' => 1));
		$temp = $update_status->response;
        
        echo '200';
	  }
?>