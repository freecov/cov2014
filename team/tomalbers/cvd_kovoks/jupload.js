<!--
    var _info = navigator.userAgent;
    var _ns = false;
    var _ns6 = false;
    var _ie = (_info.indexOf("MSIE") > 0 && _info.indexOf("Win") > 0 && _info.indexOf("Windows 3.1") < 0);
    var _ns = (navigator.appName.indexOf("Netscape") >= 0 && ((_info.indexOf("Win") > 0 && _info.indexOf("Win16") < 0 && java.lang.System.getProperty("os.version").indexOf("3.5") < 0) || (_info.indexOf("Sun") > 0) || (_info.indexOf("Linux") > 0) || (_info.indexOf("AIX") > 0) || (_info.indexOf("OS/2") > 0) || (_info.indexOf("IRIX") > 0)));
    var _ns6 = ((_ns == true) && (_info.indexOf("Mozilla/5") >= 0));

    if (_ie == true) document.writeln('<OBJECT classid="clsid:8AD9C840-044E-11D1-B3E9-00805F499D93" WIDTH = "640" HEIGHT = "300"  codebase="http://java.sun.com/update/1.4.2/jinstall-1_4-windows-i586.cab#Version=1,4,0,0"><NOEMBED><XMP>');
    else if (_ns == true && _ns6 == false) document.writeln('<EMBED \
	    type="application/x-java-applet;version=1.4" \
            CODE = "wjhk.jupload.JUploadApplet" \
            ARCHIVE = "jupload/wjhk.jupload.jar" \
            WIDTH = "640" \
            HEIGHT = "300" \
            postURL = "'+url+'" \
	    scriptable=false \
	    pluginspage="http://java.sun.com/products/plugin/index.html#download"><NOEMBED><XMP>');
//-->
