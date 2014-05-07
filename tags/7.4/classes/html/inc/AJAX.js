// AJAX Framework Version 1.4 
// Copyright 2005 Jason Graves (GodLikeMouse)
// This file is free to use and distribute under the GNU open source
// license so long as this header remains intact.
// For more information please visit http://www.godlikemouse.com
// To have changes incorporated into the AJAX Framework please contact godlikemouse@godlikemouse.com
// Supported Browsers: IE 6.0+, Opera 8+, Mozilla based browsers
//
// Developer Note: .NET WebService Methods must be tagged with [SoapRpcMethod, WebMethod] for parameters to be passed. 


function Map(){
    var len = 0;
    var keys = new Array();
    var values = new Array();

    this.get = function(key){
        var val = null;
        for(var i=0; i<len; i++){
            if(keys[i] == key){
                val = values[i];
                break;
            }//end if
        }//end for

        return val;
    }//end get()

    this.put = function(key, value){
        keys[len] = key;
        values[len++] = value;
    }//end put()

    this.length = function(){
        return len;
    }//end length()

    this.contains = function(key){
	var con = false;
        for(var i=0; i<len; i++){
            if(keys[i] == key){
                con = true;
                break;
            }//end if
        }//end for

	return con;
    }//end contains()

    this.remove = function(key){
        var keyArr = new Array();
        var valArr = new Array();
        var l = 0;
        for(var i=0; i<len; i++){
            if(keys[i] != key){
                keyArr[l] = keys[i];
                valArr[l++] = values[i];
            }//end if
        }//end for

        keys = keyArr;
        values = valArr;
	len = l;
    }//end remove()        

}//end Map

//AJAX main class
function AJAX(){

    var nameSpace = "http://tempuri.org/";
    var map = new Map();

    var getAJAXIdentity = function(){
        return "AJAX" + (AJAX.indentity++);
    }//end GetAJAXIdentity()
    
    //Overridden toString method.
    this.toString = function(){
        return "AJAX Framework Class";
    }//end toString()
    
    //Method for error handling.
    this.onError = function(error){
        alert(error);
    };//end onError()

    //Call a page with a callback function name.
    this.callPage = function(url, callbackFunction){
        var iframe = document.createElement("IFRAME");
        var IE = (navigator.appName.indexOf("Microsoft") >= 0);
        iframe.id = getAJAXIdentity();
        
        map.put(iframe.id, callbackFunction);
        
        iframe.style.display = "none";
        document.body.appendChild(iframe);
        
        if(IE){
            
            if(iframe.addEventListener){
                //Opera
                iframe.addEventListener("load", function(){
                    callbackFunction(document.frames[this.id].document.body.innerHTML);
                    this.removeNode();
                }, false);
            }
            else{
                //IE
                iframe.onreadystatechange = function(){
                    if(this.readyState == "complete"){
                        callbackFunction(document.frames[this.id].document.body.innerHTML);
                        this.removeNode();
                    }//end if
                };
            }//end tc
        }
        else{
            //Mozilla
            iframe.addEventListener("load", function(){
                callbackFunction(document.getElementById(this.id).contentDocument.body.innerHTML);
                //this.removeNode();
            }, false);
        }//end if
        
        iframe.src = url;
    };//end callPage()
	
    //Call a web service, pass any additional aruments in as "key=value"
    this.callService = function(serviceUrl, soapMethod, callbackFunction){
        var IE = (navigator.appName.indexOf("Microsoft") >= 0);
        var callServiceError = this.onError;
        
        if(IE){
            if(serviceUrl.indexOf("http://") < 0)
                serviceUrl = "http://" + serviceUrl;
            serviceUrl += "?WSDL";
            var soapEnvelope = new String();
            soapEnvelope += "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
            soapEnvelope += "<soap:Envelope xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:soap=\"http://schemas.xmlsoap.org/soap/envelope/\">";
            soapEnvelope += "<soap:Body>";
            soapEnvelope += "<" + soapMethod + " xmlns=\"" + nameSpace + "\">";
            
            if(arguments.length > 3){
                for (var i = 3; i < arguments.length; i++)
                {
                    var params = arguments[i].split("=");
                    soapEnvelope += "<" + params[0] + ">";
                    soapEnvelope += params[1];
                    soapEnvelope += "</" + params[0] + ">";
                }//end for
            }//end if
			
            soapEnvelope += "</" + soapMethod + ">";
            soapEnvelope += "</soap:Body>";
            soapEnvelope += "</soap:Envelope>";
			
            var xmlHttp = null;
            if(window.XMLHttpRequest){
                xmlHttp = new XMLHttpRequest();
                xmlHttp.callbackFunction = callbackFunction;
            }
            else{
                xmlHttp = new ActiveXObject("MSXML2.XMLHTTP");
            }//end if
            
            
            xmlHttp.onreadystatechange = function(){
                
                if(xmlHttp.readyState == 4){
                    
                    var cb = null;
                    if(xmlHttp.callbackFunction){
                        //opera
                        var response = xmlHttp.responseXML.getElementsByTagName(soapMethod + "Result")[0];
                        if(!response)
                            response = xmlHttp.responseXML.getElementsByTagName(soapMethod + "Response")[0];
                        if(!response){
                            callServiceError("WebService does not contain a Result/Response node");
                            return;
                        }//end if
                        
                        xmlHttp.callbackFunction(xmlHttp.responseXML.getElementsByTagName(soapMethod + "Result")[0].innerHTML);
                    }
                    else if(callbackFunction){
                        //IE
                        var responseXml = new ActiveXObject('Microsoft.XMLDOM');
                        responseXml.loadXML(xmlHttp.responseText);
                            
                        var responseNode = responseXml.selectSingleNode("//" + soapMethod + "Response");
                        if(!responseNode)
                            responseNode = responseXml.selectSingleNode("//" + soapMethod + "Result");
                        if(!responseNode)
                            callServiceError("Response/Result node not found.\n\nResponse:\n" + xmlHttp.responseText);
                            
                        var resultNode = responseNode.firstChild;
                        if (resultNode != null){
                            try{
                                callbackFunction(resultNode.text);
                            }
                            catch(ex){
                                callServiceError(ex);
                            }//end tc
                        }
                        else{
                            try{
                                callbackFunction();
                            }
                            catch(ex){
                                callServiceError(ex);
                            }//end tc
                        }//end if
                    }//end if
                }//end if
            };
            
            xmlHttp.open("POST", serviceUrl, true);			
            xmlHttp.setRequestHeader("Content-Type", "text/xml");
            xmlHttp.setRequestHeader("SOAPAction", nameSpace + soapMethod);
            try{
                xmlHttp.send(soapEnvelope);
            }
            catch(ex){
                serviceCallError(ex);
            }
        }
        else{
            var soapCall = new SOAPCall();
            var soapParams = new Array();
            var headers = new Array();
            var soapVersion = 0;
            var object = nameSpace;
            
            if(serviceUrl.indexOf("http://") < 0)
                serviceUrl = document.location + serviceUrl;
            
            soapCall.transportURI = serviceUrl;
            soapCall.actionURI = nameSpace + soapMethod;
            
            for(var i=3; i<arguments.length; i++){
                var params = arguments[i].split("=");
                soapParams.push( new SOAPParameter(params[1],params[0]) );
            }//end for
            
            try{
                soapCall.encode(soapVersion, soapMethod, object, headers.length, headers, soapParams.length, soapParams);
            }
            catch(ex){
                serviceCallError(ex);
            }//end tc
		
            try{
                netscape.security.PrivilegeManager.enablePrivilege("UniversalBrowserRead");
            } 
            catch(ex){
                return false;
            }
            
            try{
                soapCall.asyncInvoke(
                    function(resp,call,status){

                    if(resp.fault)
                        return callServiceError(resp.fault);
                    if(!resp.body){
                        callServiceError("Service " + call.transportURI + " not found.");
                    }
                    else{
                        try{
                            callbackFunction(resp.body.firstChild.firstChild.firstChild.data);
                        }
                        catch(ex){
                            callServiceError(ex);
                        }//end tc
                    }//end if
                }
                );
            }
            catch(ex){
                serviceCallError(ex);
            }//end tc
                        
        }//end if
		
    }//end callService()
	
    //Method for setting the namespace
    this.setNameSpace = function(ns){
        nameSpace = ns;
    }//end setNameSpace()
	
    //Method for returning the namespace
    this.getNameSpace = function(){
        return ns;
    }//end getNameSpace()
	
}//end AJAX()

AJAX.indentity = 0;

