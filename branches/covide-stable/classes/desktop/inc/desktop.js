function toggle_notes(state) {
	if (document.getElementById('ownnotes')) {
		if (state == 'active') {
			document.getElementById('ownnotes').style.visibility = 'visible';
		} else {
			document.getElementById('ownnotes').style.visibility = 'hidden';
		}
	}
}

if (document.getElementById('block_twitter') && document.getElementById('twitterspan')) {
	document.getElementById('twitterspan').innerHTML=loadXMLContent('index.php?mod=desktop&action=loadtwittercontent');
}

function sendTweet() {
	var status = document.getElementById('desktopnewtweet').value;
	document.getElementById('desktopnewtweet').value='';
	if (status.length > 3 && status.length < 140) {
		//escape the string before we send it
		status = encodeURIComponent(status);
		status = status.replace(/~/g,'%7E').replace(/%20/g,'+');
		loadXML('index.php?mod=twitter&action=newtweet&status='+status);
	}
}
