var arrTemp=self.location.href.split("?");
var picUrl = (arrTemp.length>0)?arrTemp[1]:"";
var NS = (navigator.appName=="Netscape")?true:false;
var a = 5;

function FitPic() {
	iWidth = (NS)?window.innerWidth:document.body.clientWidth;
	iHeight = (NS)?window.innerHeight:document.body.clientHeight;
	iWidth = document.images[0].width - iWidth;
	iHeight = document.images[0].height - iHeight;
	window.resizeBy(iWidth+a, iHeight+a);
	self.focus();
};

addLoadEvent(FitPic());
