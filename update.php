<html>
<head>
<title>Twitter oAuth | Update your status</title>
<link rel="stylesheet" href="style.css" type="text/css" media="screen, projection" />
</head>
<body>
<h1>Hello and Welcome to the oAuth Tutorial</h1>
<?php $_SESSION['twitter_profile']; ?>
<div id="form"><!--Start form-->
<p>Twitter Handle: <?php echo $username ?></p>
<p>Profile Picture: <br /><?php echo "<img src='$profilepic' />" ?><br /></p>
<label>Update Twitter Timeline</label><br />
<form method='post' action='index.php'>

<div align="left" id="character-count"><!--Start Character Count-->
 <div id="count">140</div>
 <div id="barbox"><div id="bar"></div></div>
 </div><!--End Character Count-->
<br />
<textarea  name="tweet" cols="50" rows="5" id="tweet" ></textarea>
<br />
<input type='submit' value='Tweet' name='submit' id='submit' />
</form>
</div><!--End Form-->

</body>
</html>