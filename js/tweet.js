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

last_id = 0;


function getTweets(hashtag,cb)
{
	 if (hashtag.length <= 16){
		var url="http://search.twitter.com/search.json?q=%23"+hashtag+"&rpp=25&since_id="+last_id+"&callback=?";
	} else {
		var url="http://search.twitter.com/search.json?q="+hashtag+"&rpp=25&since_id="+last_id+"&callback=?";	
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
			console.error(data);
			cb(data);
			return;
    	
		}
	});
	//timeout = setTimeout(function(){getTweets(elem)},timeoutSpeed);
	return(false);
	
}

