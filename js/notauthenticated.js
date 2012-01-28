jQuery.fn.reverse=Array.prototype.reverse;
String.prototype.links=function() {
    return this.replace(/[A-Za-z]+:\/\/[A-Za-z0-9-_]+\.[A-Za-z0-9-_:%&\?\/.=]+/g,
    function(a) {
        return '<a href="' + a + '" target="_new">' + a + '</a>';
    });
};
String.prototype.user=function() {
    return this.replace(/[@]+[A-Za-z0-9-_]+/g,
    function(b) {
        var username = b.replace("@","")
        return '<a class="twitter-anywhere-user" href="http://twitter.com/' + username + '" target="_new">' + b + '</a>';
    });
};
String.prototype.hashtags=function() {
    return this.replace(/[#]+[A-Za-z0-9-_]+/g,
    function(c) {
        var hashtag=c.replace("#","")
        return c.link("http://www.twtchat.com/room/"+hashtag);
    });
};
String.prototype.RTs=function() {
    if (this.search(/^RT @/i) != -1) {
    	return (true);
    }
};

function getTweets(elem) {
    elem=jQuery(elem);
    if (tweettag.length <= 16){
		var url="http://search.twitter.com/search.json?q=%23"+tweettag+"&rpp=25&since_id="+last_id+"&callback=?";
	} else {
		var url="http://search.twitter.com/search.json?q="+tweettag+"&rpp=25&since_id="+last_id+"&callback=?";	
	}
	jQuery.ajax({
		url: url,
		type: 'GET',
		dataType: 'jsonp',
		timeout: 10000,
		dataFilter: function(data) {
			if (typeof (JSON) !== 'undefined' && typeof (JSON.parse) === 'function') {
				return JSON.parse(data);
			} else {
				return eval('(' + data + ')');
			}
		},
		success: function(data){
    		jQuery(data.results).reverse().each(function() { //NEWEST AT TOP
    		    if(jQuery('#'+this.id_str).length==0) { //I DON'T EXIST, CREATE ME
    		        var posted= new Date(this.created_at); //GIVE ME A BEAUTIFUL DATE
    		        //var tweet='<p id='+this.id_str+' class='+this.from_user.toLowerCase()+'><a href="http://twitter.com/'+this.from_user+'" target="_new"><img width="48" height="48" border="0" alt="" align="left" src="'+this.profile_image_url+'" class="profilePic" ></a> <span class="smallTxt right" style="text-align:right;"><a href="http://threadir.com/start?replyTweet='+this.id_str+'" target="_new"><img src="http://cdn.threadir.com/img/write.png?48201" title="Start Thread at threadir.com" border="0" style="opacity:0.3;filter:alpha(opacity=30);" /></a><span id="user'+this.id_str+'"><a href="#" onClick="user(\''+this.id_str+'\'); return false;"><img src="../images/user.gif" title="User Control" border="0" style="float: right;" /></a></span><br><span id="userControl'+this.id_str+'" style="display:none;text-align:center;" class="small button white"><a href="#" onClick="quickFeature(\''+this.from_user+'\',\''+this.id_str+'\'); return false;">Feature</a> | <a href="#" onClick="quickBlock(\''+this.from_user+'\',\''+this.id_str+'\'); return false;">Block</a></span></span> <a class="twitter-anywhere-user" href="http://twitter.com/'+this.from_user+'" target="_new">'+this.from_user+'</a> '+this.text.links().user().hashtags()+'<span class="smallTxt"> -'+dateFormat(posted, "h:MM TT mmm dS, yyyy")+'</span><br style="clear:both;" /></p>';
					
					 var tweet='<p id='+this.id_str+' class='+this.from_user.toLowerCase()+'><a href="http://twitter.com/'+this.from_user+'" target="_new"><img width="48" height="48" border="0" alt="" align="left" src="'+this.profile_image_url+'" class="profilePic" ></a> <span class="smallTxt right" style="visibility:hidden;text-align:right;"><a href="http://threadir.com/start?replyTweet='+this.id_str+'" target="_new"><img src="http://cdn.threadir.com/img/write.png?48201" title="Start Thread at threadir.com" border="0" style="opacity:0.3;filter:alpha(opacity=30);" /></a><span id="user'+this.id_str+'"><a href="#" onClick="user(\''+this.id_str+'\'); return false;"><img src="../images/user.gif" title="User Control" border="0" style="float: right;" /></a></span><br><span id="userControl'+this.id_str+'" style="display:none;text-align:center;" class="small button white"><a href="#" onClick="quickFeature(\''+this.from_user+'\',\''+this.id_str+'\'); return false;">Feature</a> | <a href="#" onClick="quickBlock(\''+this.from_user+'\',\''+this.id_str+'\'); return false;">Block</a></span></span> <a class="twitter-anywhere-user" href="http://twitter.com/'+this.from_user+'" target="_new">'+this.from_user+'</a> '+this.text.links().user().hashtags()+'<span class="smallTxt"> -'+dateFormat(posted, "h:MM TT mmm dS, yyyy")+'</span><br style="clear:both;" /></p>';
    		       
					
    		        if (last_id < this.id_str) { //DON'T LET .REVERSE() RUIN OUR PARTY
    		        	last_id=this.id_str;
    		        }
    		        elem.prepend(tweet);
					//CHECK IF POST IS FROM FEATURED TWEETER
					if (featuredTweeter !== "") {
						var temp = new Array();
						temp = featuredTweeter.split(',');

						jQuery.each(temp, function() {
							jQuery("." + this).addClass("featuredTweet");
						});
					}
					//CHECK IF POST IS FROM BLOCKED TWEETER
					if (blockTweeter !== "") {
						var blockTemp = new Array();
						blockTemp = blockTweeter.split(',');

						jQuery.each(blockTemp, function() {
							jQuery("." + this).addClass("blockedTweet").hide();
						});
					}
    		        //CHECK FOR RETWEETS
        			if (this.text.RTs()) {
    		        	jQuery('#'+this.id_str).addClass("RT");
						if ($('#blockRTs').is(':checked')) {
	    		        	jQuery('#'+this.id_str).addClass("blockedTweet");
						}
					}

					
    		        
					//SHOW THE TWEET TO THE WORLD
    		        jQuery('#'+this.id_str).hide();
    		        jQuery('#'+this.id_str+':not(".blockedTweet")').slideDown(1000);
					//jQuery('#'+this.id_str+':not(".blockedTweet")').animate({"height": "show", "opacity": "show"}, 1500);
    		    }
    		});
			jQuery("#LoadingTweets").remove();
		}
	});
	timeout = setTimeout(function(){getTweets(elem)},timeoutSpeed);
	return(false);
}
