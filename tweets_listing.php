<html>
<head>
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
$(document).ready(function()
{

        jmessage = $("#message");
		jnotice = $("#notice");
		jsubmitTweet = $("#submitTweet");
		jplayPause = $("#playPause");
	
		tweettag="<?php echo $_GET['room']; ?>";
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
				url: '/inserttweet.php',
				type: 'POST',
				data: {message: jmessage.val(), replyID: $("#replyID").val(), room: "<?php echo $_GET['room'];?>", ot: "<?php echo $_SESSION['ot'];?>", ots: "<?php echo $_SESSION['ots'];?>"},
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
							limitText(jmessage,$('#countdown'),132);
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

$("#tweet").keyup(function()
{
var box=$(this).val();
var main = box.length *100;
var value= (main / 140);
var count= 140 - box.length;

if(box.length <= 140)
{
$('#count').html(count);
$('#bar').animate(
{
"width": value+'%',
}, 1);
}
else
{
alert('Character Limit Exceeded!');

;
}
return false;
});

});
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
		limitText(jmessage,$('#countdown'),132);
	}

	function retweet(retweetName, retweetMessage) {
		window.scrollTo(0,0);
		jmessage.val("RT @" + retweetName + "\: " + retweetMessage);
		jmessage.selectRange(0,0);
		//jmessage.val("RT @");
		limitText(jmessage,$('#countdown'),132);
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
	    replaceTag =  / #<?php echo $_GET['room'];?>$/gi;
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
</head>
<body>
<div id="header">
	<span class="btn">
		<a href="<?php if( isset($_SESSION['ot']) && isset($_SESSION['ots']) ){?>/index.php?action=signout<?php }else{ echo $url; } ?>" class="large button1"><?php if( isset($_SESSION['ot']) && isset($_SESSION['ots']) ){?>Sign Out<?php }else{ ?>Sign In<?php }?></a>
	</span>
	<span class="big"><?php if( isset($_SESSION['ot']) && isset($_SESSION['ots']) ){ echo '<img src="' . $profilepic .'" border=0 alt="' . $username . '" >'; } ?><span id="TCName">Chirped.it</span></span>
	<span class="hashInput">#<form method="POST" action="/" id="hashEnter" style="display: inline;">
		<input type="text" class="" name="hashTag" id="hashTag" value="<?php echo $_GET['room'];?>" />
		<a href="#" class="headerButton large button1" onClick="$('form#hashEnter').submit(); return false;">Go &raquo</a>
	</form></span>
	<div id="description" style="font-size: 9px;">
	</div>
</div><div id="page">
<div id="inputArea">

	<!-- USTREAM EMBED -->
	<div id="videoEmbedArea"></div>


    <?php if ( isset($_SESSION['ot']) && isset($_SESSION['ots']) ){ ?>
 <!--   <form id="messageEnter" action="" method="post">
		<span class="right countdown" id="countdown">133</span>
		<label id="messageText" for="message">Message to #<?php echo $_GET['room'];?>:</label>
		<textarea tabindex="1" cols="50" rows="2" onKeyUp="limitText(jQuery('#message'),jQuery('#countdown'),133);" onKeyDown="limitText(jQuery('#message'),jQuery('#countdown'),133);" id="message" name="message"></textarea>
		<input type="hidden" value="" id="replyID" name="replyID">
		<div class="left transportControls">
			<a class="button small white" id="playPause" href="#">Pause</a>
			<a onClick="jQuery('#speedCentral').slideToggle('slow'); return false;" class="button small white" href="#">Refresh Speed</a>
			<a class="button small white" id="toggleFont" href="#">Toggle Font</a>
			<a class="button small white" id="shareChat" href="#">Share Link</a>
		</div>
		<input type="submit" value="update" tabindex="2" style="margin: 4px 5px 0 0" class="button right" id="submitTweet">
	</form>-->
    <?php }else{ ?>
        <div class="transportControls" style="text-align: right;">
		<a href="#" id="playPause" class="button small white">Pause</a>
		<a href="#" class="button small white" onClick="jQuery('#speedCentral').slideToggle('slow'); return false;">Refresh Speed</a>
		<a href="#" id="toggleFont" class="button small white">Toggle Font</a>
	</div>
    <?php } ?>
	<div id="notice"></div>


<!-- Refresh Speed -->
<link type="text/css" href="/css/themes/default/ui.all.css" rel="stylesheet" />
<script type="text/javascript" src="/js/ui/ui.core.js"></script>
<script type="text/javascript" src="/js/ui/ui.slider.js"></script>
<style type="text/css">
	#speedInfo {
		padding-bottom: 10px !important;
	}
	#speedCentral {
		display: none;
		background-color: #f4f4f4;
		-moz-border-radius: 5px;
		-webkit-border-radius: 5px;
		border: 1px solid #dddddd;
		padding: 10px 20px; !important
	}
</style>
<script type="text/javascript">
jQuery(function() {
	jQuery("#speedSlider").slider({
		range: "min",
		value: 10,
		min: 5,
		max: 60,
		slide: function(event, ui) {
			jQuery("#speedInfo").html("Refresh every " + ui.value + ' seconds');
		},
		stop: function(event, ui) {
			clearTimeout(timeout);
			jQuery('#notice').fadeOut('slow');
			timeoutSpeed = jQuery("#speedSlider").slider("value") *1000;
			getTweets('#chirped');
		}
	});
	jQuery("#speedInfo").html("Refresh every " + jQuery("#speedSlider").slider("value") + ' seconds');
});
</script>

<div id="speedCentral">
	<div class="small button right" onClick="jQuery('#speedCentral').slideUp('slow'); return false;">x</div>
	<div id="speedInfo"></div>
	<div id="speedSlider"></div>
</div>
<!-- Refresh Speed -->
<!-- User Control -->

<div id="userControl">
	<div class="small button right" onClick="jQuery('#userControl').slideUp('slow'); return false;">x</div>

	<div><strong>Feature and block users to your personal taste.</strong></div>
	<div>Add multiple users by separating each name with a comma.</div>
	<div style="clear: both; margin-top: 10px;">Feature: <input type="text" id="featured" /></div>
	<div style="clear: both; margin-top: 10px;">Block: <input type="text" id="block" /></div>
	<div style="clear: both;"></div>
	<div style="border-top: 1px solid #ddd; padding-top: 10px; margin-top: 20px;""><strong>Block retweets?</strong> <input type="checkbox" id="blockRTs" /></div>
</div>

<!-- User Control -->

	<!-- Video Control -->
	<div id="externalControl">
		<span class="ui-state-default ui-corner-all right" title="Hide"><span class="ui-icon ui-icon-close" onClick="jQuery('#externalControl').slideUp('slow'); return false;"></span></span>

		<div><strong>Add a Ustream video feed.</strong></div>
		<div style="clear: both; margin-top: 10px;">
			Ustream channel URL: <input type="text" id="videoURL" style="width:300px; margin-right: 10px;" /><input type="submit" id="addVideo" class="btn" value="Add Video" />
		</div>
		<div style="clear: both;"></div>
	</div>
	<!-- Video Control -->

</div>

<div id="chirped">
<div id="LoadingTweets" style="text-align: center;">LOADING POSTS:<br/><img src="/images/horizontal-loading.gif" /></div></div>

</div>
