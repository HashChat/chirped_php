<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Chriped.it</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/> 
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7/jquery-ui.min.js"></script>
<script type="text/javascript" src="/js/date.format.js"></script>
<script type="text/javascript" src="/js/notauthenticated.js"></script>
<script src="http://platform.twitter.com/anywhere.js?id=QErzC5nolnbS8JC2lfqZow&v=1" type="text/javascript"></script>
<script type="text/javascript">
	twttr.anywhere(function (T) {
		T.hovercards({ linkify: false });
	});
</script>
<script type="text/javascript">
	$(document).ready(function() {
	
		jmessage = $("#message");
		jnotice = $("#notice");
		jsubmitTweet = $("#submitTweet");
		jplayPause = $("#playPause");
	
		tweettag="";
    	me="";
    	featuredTweeter = "";
    	blockTweeter = "";
    	last_id = 0;
    	timeoutSpeed = 10000;

		
		//LAME IPHONE TEST
		if ((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i))) {
		} else {
			//SHOW SMART PAUSING ONLY WHEN NECESSARY
			$(window).scroll(function(){

				if (jnotice.text() != "PAUSED...") { //DON'T SCREW UP REGULAR PAUSING
					if ($(document).scrollTop() > ($(window).height()*2)) {
						jnotice.css({
							"position" : "fixed",
							"top" : "0",
							"right" : "0",
							"border-bottom" : "5px solid #CCC"
						});
				
						if (jplayPause.text() == 'Pause') {
							clearTimeout(timeout);
							jplayPause.text("Play");
							jnotice.html('SMART PAUSING').css('background','#FF9999').show();
							return false;
						}

					} else if ($(document).scrollTop() < $(window).height()) {
						jnotice.css({
							"position" : "static",
							"border-bottom" : "none"
						});
				
						if (jplayPause.text() == 'Play') {
							getTweets('#chirped');
							jplayPause.text("Pause");
							jnotice.slideUp('fast').fadeOut('slow');
							return false;
						}
					}
				}
	
			}); // END WINDOW SCROLLING FUNCTION
		} // END LAME IPHONE TEST

	    $('#chirped').each(function() {
	        getTweets(this);
	    });
	    
		jsubmitTweet.click(function(){
			if (jmessage.val() != '') {

			jnotice.html('<img src="/images/horizontal-loading.gif" />').css('background', 'none').show();

			$.ajax({
				url: '/submitmessage.php',
				type: 'POST',
				data: {message: jmessage.val(), replyID: $("#replyID").val(), room: ""},
				dataType: 'text',
				timeout: 15000,
				error: function(){
					jnotice.text('Technical difficulties with Twitter, please repost your message').css('background', '#FF9999').show().animate({opacity: 1.0}, 3000).fadeOut(1500);
					return false;
				},
				success: function(httpcode){
					switch (httpcode) {
						case "200":
							jnotice.text("Posted. Will take a short time to show...").css('background', '#74D12E').show().animate({opacity: 1.0}, 3000).fadeOut(1500);
							jmessage.val('');
							$('#replyID').val('');
							limitText(jmessage,$('#countdown'),138);
							break;
						case "400":
							jnotice.text("Post failed: Rate Limit Exceeded").css('background', '#FF9999').show().animate({opacity: 1.0}, 3000).fadeOut(1500);
							break;
						case "401":
							jnotice.text("Post failed: Invalid Credentials - Redirecting to Chirped.it homepage").css('background', '#FF9999').show().animate({opacity: 1.0}, 3000).fadeOut(1500);
							//SHOULD PROBABLY REDIRECT TO THE LOGIN SCREEN...
							setTimeout("window.location='http://www.twtchat.com'",3000);
							break;
						case "502":
							jnotice.text("Post failed: Twitter is Down").css('background', '#FF9999').show().animate({opacity: 1.0}, 3000).fadeOut(1500);
							break;
						case "503":
							jnotice.text("Post failed: Twitter is Overloaded").css('background', '#FF9999').show().animate({opacity: 1.0}, 3000).fadeOut(1500);
							break;
						default :
							jnotice.text("Post failed: Code " + httpcode).css('background', '#FF9999').show().animate({opacity: 1.0}, 3000).fadeOut(1500);
							break;
					}
				}
			});
		} else {
			jnotice.text('No message entered...').css('background', '#FF9999').show().animate({opacity: 1.0}, 3000).fadeOut(1500);
			return false;
		}
		return false;
		});

		jplayPause.click(function(){ 
	
			if (jplayPause.text() == 'Pause') {
				clearTimeout(timeout);
				jplayPause.text("Play");
				jnotice.html('PAUSED...').css('background','#FF9999').show();
				return false;
			} else {
				getTweets('#chirped');
				jplayPause.text("Pause");
				jnotice.fadeOut('slow', function(){
					jnotice.html('');
				});
				return false;
			}
		});
		
		
		//USTREAM STUFF
		$("#addVideo").click(function(){ 
			var url = $("#videoURL").val();
			url = url.replace("http://www.ustream.tv/channel/","");
			url = "http://api.ustream.tv/json/channel/"+url+"/getCustomEmbedTag?params=autoplay:true;mute:false;height:240;width:300";
			//alert(url);
			$.ajax({
				url: url,
				type: 'GET',
				dataType: 'jsonp',
				timeout: 10000,
				success: function(data){
					//alert(data);
					$("#videoEmbedArea").html(data).fadeIn(3000);
					$("#message").css({"width" : "425px", "height" : "150px"});
					$(".transportControls").css({"float" : "none"});
					$("#externalControl").slideUp("slow");
					if ($.browser.msie) {
						if(parseInt($.browser.version) == 6) {
							$('#message').css({"width" : "410px", "height" : "150px"});
						}
					} 
    			}
			});

		});
		//USTREAM STUFF END
		
		$("#shareChat").click(function(){
			$("#message").val("Join me for a #" + tweettag + " Chirped.it at: " + location.href);
			return false;
    	});

		$("#blockRTs").click(function(){
			if ($('#blockRTs').is(':checked')) {
				$(".RT").addClass("blockedTweet").hide();
			} else {
				$(".RT").removeClass("blockedTweet").show();
			}
    	});

		$("#toggleFont").click(function(){
 			if (!$("head").data('small')) {
 				$("head").data('small' , true).append("<link id=\"smallStyle\" rel=\"stylesheet\" href=\"/css/style-small.css\" type=\"text/css\" media=\"screen\" \/>");
 			} else {
 			 	$("head").data('small' , false);
 			 	$("#smallStyle").remove();
 			}
 			return false;
 		});

	    $("form#hashEnter").submit(function() {
	   		var string = $("#hashTag").val();
			//alert(string);
			string = string.replace("#","");
			if (string == "" || string == "Enter hashtag to follow") {
				$("#notice").html('Please choose a hashtag to follow...').css('background', '#FF9999').show().animate({opacity: 1.0}, 3000).fadeOut(1500);
				$("#hashTag").focus();
				return false;
			}
			string = string.replace(" ","");
			window.location="http://www.twtchat.com/index.php?room=" + string;
			return false;
	    });
		$("#hashTag").focus(function() {
			if( this.value == this.defaultValue ) {
				this.value = "";
			}
		}).blur(function() {
			if( !this.value.length ) {
				this.value = this.defaultValue;
			}
		});
			
	}); // END DOCUMENT READY
</script>
<script type="text/javascript">
	function limitText(limitField, limitCount, limitNum) {
		if (limitField.val().length > limitNum) {
			limitCount.text( limitNum - limitField.val().length );
			jsubmitTweet.attr('disabled', 'disabled').val('too long').css({'cursor' : 'default'});
		} else {
			limitCount.text( limitNum - limitField.val().length );
			jsubmitTweet.removeAttr('disabled').val('update').css({'cursor' : 'pointer'});
		}
		if (limitField.val() == '') {
			$('#replyID').val('');
		}
	}

	function reply(replyTo, replyID) {
		window.scrollTo(0,0);
		jmessage.val("").focus().val(jmessage.val() + "@" + replyTo + " ");
		$("#replyID").val(replyID);
		$("#"+replyID+"").find(".mention").each(function(){
			jmessage.val(jmessage.val() + $(this).text() + " ");
			jmessage.selectRange(replyTo.length+2, 140);
		});
		limitText(jmessage,$('#countdown'),138);
	}

	function retweet(retweetName, retweetMessage) {
		window.scrollTo(0,0);
		jmessage.val("RT @" + retweetName + "\: " + retweetMessage);
		jmessage.selectRange(0,0);
		//jmessage.val("RT @");
		limitText(jmessage,$('#countdown'),138);
	}
	
	function user(id) {
		$("#userControl"+id).slideToggle("fast");
		//alert("Something cool coming?");
	}
	
	function quickFeature(name, id) {
		$("#userControl"+id).slideToggle("fast");
		t1.add(name);
		return false;
	}

	function quickBlock(name, id) {
		$("#userControl"+id).slideToggle("fast");
		t2.add(name);
		return false;
	}

	function makeSafe(str) {
	    replaceTag =  / #$/gi;
	    str=str.replace(/'/g,"");
	    str=str.replace(/\n/g,"");
	    str=str.replace(replaceTag,"");
	    return str;
	};

	function favorite(id)	{
		$.ajax({
			url: '/favorite.php',
			type: 'POST',
			data: { favorite: id },
			dataType: 'text',
			timeout: 5000,
			error: function(){
				jnotice.text('Something is totally wack.').css('background', '#FF9999');
				return false;
			},
			success: function(httpcode){
				$("#loading_check").hide();
				switch (httpcode) {
					case "200":
						$("#star"+id).html("<img src='http://static.twitter.com/images/icon_star_full.gif' border='0' alt='' />");
						jnotice.text("Added to Favorites").css('background', '#74D12E').show().animate({opacity: 1.0}, 3000).fadeOut(1500);
						break;
					case "400":
						jnotice.text("Rate Limit Exceeded").css('background', '#FF9999').show().animate({opacity: 1.0}, 3000).fadeOut(1500);
						break;
					case "401":
						jnotice.text("Invalid Credentials").css('background', '#FF9999').show().animate({opacity: 1.0}, 3000).fadeOut(1500);
						break;
					case "403":
						$("#star"+id).html("<img src='http://static.twitter.com/images/icon_star_full.gif' border='0' alt='' />");
						jnotice.text("Already favorited").css('background', '#74D12E').show().animate({opacity: 1.0}, 3000).fadeOut(1500);
						break;
					case "404":
						jnotice.text("Can't favorite: Tweet deleted by poster").css('background', '#FF9999').show().animate({opacity: 1.0}, 3000).fadeOut(1500);
						break;
					case "502":
						jnotice.text("Twitter is Down").css('background', '#FF9999').show().animate({opacity: 1.0}, 3000).fadeOut(1500);
						break;
					case "503":
						jnotice.text("Twitter is Overloaded").css('background', '#FF9999').show().animate({opacity: 1.0}, 3000).fadeOut(1500);
						break;
					default :
						jnotice.text("Error: Code " + httpcode).css('background', '#FF9999').show().animate({opacity: 1.0}, 3000).fadeOut(1500);
						break;
				}
			}
		});

	}
</script>
<link rel="stylesheet" href="/css/style.css" type="text/css" media="screen" />
<!-- required for TextboxList -->
<script src="/js/GrowingInput.js" type="text/javascript" charset="utf-8"></script>
<script src="/js/TextboxList.js" type="text/javascript" charset="utf-8"></script>		
<link rel="stylesheet" href="/css/TextboxList.css" type="text/css" media="screen" charset="utf-8" />
		<script type="text/javascript" charset="utf-8">		
			$(function(){
				// With custom adding keys 
				t1 = new TextboxList('#featured', {unique: false, inBetweenEditableBits: false, bitsOptions:{editable:{addKeys: 188}}});
								
				t1.addEvent('bitBoxAdd', function(){
					//alert("Added");
					$("#chirped p").removeClass("featuredTweet");
					featuredTweeter = $("#featured").val().toLowerCase().replace(/ /g,'');
					if (featuredTweeter == "") {
						return false;
					} else {
						var temp = new Array();
						temp = featuredTweeter.split(',');
						for (var i=0; i<temp.length; i++) {
							$('.'+temp[i]).addClass("featuredTweet");
						}
					}
				});

				t1.addEvent('bitBoxRemove', function(){
					//alert("Removed");
					$("#chirped p").removeClass("featuredTweet");
					featuredTweeter = $("#featured").val().toLowerCase().replace(/ /g,'');
					if (featuredTweeter == "") {
						return false;
					} else {
						var temp = new Array();
						temp = featuredTweeter.split(',');
						for (var i=0; i<temp.length; i++) {
							$('.'+temp[i]).addClass("featuredTweet");
						}
					}
				});


				// With custom adding keys 
				t2 = new TextboxList('#block', {unique: false, inBetweenEditableBits: false, bitsOptions:{editable:{addKeys: 188}}});
								
				t2.addEvent('bitBoxAdd', function(){
					//alert("Blocked");
					$("#chirped p").removeClass("blockedTweet");
					$("#chirped p:hidden").show();
					blockTweeter = $("#block").val().toLowerCase().replace(/ /g,'');
					if (blockTweeter == "") {
						return false;
					} else {
						var blockTemp = new Array();
						blockTemp = blockTweeter.split(',');

						for (var i=0; i<blockTemp.length; i++) {
							$('.'+blockTemp[i]).hide();
						}
					}
				});

				t2.addEvent('bitBoxRemove', function(){
					//alert("UnBlocked");
					$("#chirped p").removeClass("blockedTweet");
					$("#chirped p:hidden").show();
					blockTweeter = $("#block").val().toLowerCase().replace(/ /g,'');
					if (blockTweeter == "") {
						return false;
					} else {
						var blockTemp = new Array();
						blockTemp = blockTweeter.split(',');

						for (var i=0; i<blockTemp.length; i++) {
							$('.'+blockTemp[i]).hide();
						}
					}
				});
								
			});
		</script>
<!-- End required for TextboxList -->

<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;"/>
<script type="text/javascript">
//<![CDATA[
function OpenWin() {
		window.open("http://www.twtchat.com/index.php", "Child",'width=800,height=400,left=150,top=100,scrollbar=no,resize=no');
}
if (window.top !== window.self) {
	var head_node = document.getElementsByTagName('head')[0];
	var link_tag = document.createElement('link');
	link_tag.setAttribute('rel', 'stylesheet');
	link_tag.setAttribute('type', 'text/css');
	link_tag.setAttribute('href', 'css/embed.css');
	head_node.appendChild(link_tag);
	$(document).ready(function() {
		$("span.btn").html("<a href='#' onClick=\"$('span.hashInput').slideToggle()\" class=\"small button\">Change Room<\/a> <a href='' onClick='OpenWin(); return false;' class='small button'>Sign in with Twitter<\/a>");
 		$("head").data('small' , true).append("<link id=\"smallStyle\" rel=\"stylesheet\" href=\"/css/style-small.css\" type=\"text/css\" media=\"screen\" \/>");
 	});
}
//]]>
</script>
</head>
<body>
<div id="header">
	<span class="btn">
		<a href="<?php if( isset($_SESSION['ot']) && isset($_SESSION['ots']) ){?>/index.php?action=signout<?php }else{ echo $url; } ?>" class="large button"><?php if( isset($_SESSION['ot']) && isset($_SESSION['ots']) ){?>Sign Out<?php }else{ ?>Sign In<?php }?></a>
	</span>
	<span class="big"><?php if( isset($_SESSION['ot']) && isset($_SESSION['ots']) ){ echo '<img src="' . $profilepic .'" border=0 alt="' . $username . '" >'; } ?><span id="TCName">Chirped.it</span></span>
	<span class="hashInput">#<form method="POST" action="/" id="hashEnter" style="display: inline;">
		<input type="text" class="" name="hashTag" id="hashTag" value="Enter hashtag to follow" />
		<a href="#" class="headerButton large button" onClick="$('form#hashEnter').submit(); return false;">Go &raquo</a>
	</form></span>
</div><div id="page">
	<?php if( isset($_SESSION['ot']) && isset($_SESSION['ots']) ){?><div style="margin-bottom: 5px;" class="right countdown">Hi, <?php echo $name;?>!</div><?php } ?>
	<div id="notice"></div>
	<div id="about">
			<h1>How to use Chirped</h1>
    	        	<div class="block">
    	        		<h2>1</h2>
    	        		<p><b>Sign in to Chirped.it.</b></p>
    	        		<p><a href="https://twitter.com/signup" target="_new">Signup with Twitter</a> if you don't have an account.</p>
    	        		<p>Our login securely authenticates you with Twitter. Once authenticated, you will be returned directly to Chirped.it.</p>
    	        		<p>If you ever want to sign in as a different user, sign out at Twitter and return to Chirped.it.</p>
    	        	</div>
    	        	<div class="block">
    	        		<h2>2</h2>
    	        		<p><b>Choose hashtag to follow.</b></p>
						<p><a href="http://twitter.pbwiki.com/Hashtags" target="_new">Hashtags</a> identify specific topics and those hashtags allow Chirped.it to connect you with people talking about similar things.</p>
						<p>Chirped.it helps put your blinders on to the Twitter-sphere while you monitor and chat about one topic.</p>
						<p>Choosing a hashtag directs you to a Chirped.it room.</p>
    	        	</div>
    	        	<div class="block">
    	        		<h2>3</h2>
    	        		<p><b>Converse in real-time.</b></p>
       					<p>Each tweet automatically gets the hashtag added and the room auto-updates.</p>
						<p>You can use the "User Control" area to feature people you like or to block spammers.</p>
						<p>"Smart pausing" has been added so when you scroll down the page, it will not refresh, helping you avoid replying to the wrong person.</p>
    	        	</div>

			<p class="credits">Follow us on Twitter today: <a href="http://twitter.com/Chirped" target="_new">@Chirped</a> | <a href="privacy.html">Privacy Policy</a></p>
	</div>
</div>
</body>
</html>
