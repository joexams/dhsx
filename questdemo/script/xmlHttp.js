//xmlHttp.js
var Request = new Object();
Request.reqList = [];
function createXMLRequest()
{
    var xmlHttp=false; 
    try { 
        xmlHttp = new ActiveXObject("Msxml2.XMLHTTP"); 
    } 
    catch (e){ 
      try { 
          xmlHttp = new ActiveXObject("Microsoft.XMLHTTP"); 
      } 
     catch (E){ 
          xmlHttp = false; 
      } 
    }
    if (!xmlHttp && typeof XMLHttpRequest!='undefined') 
    { 
        xmlHttp = new XMLHttpRequest(); 
    } 
    return xmlHttp;
}

Request.send = function(url, method, callback, data)
 { 
    var xmlHttp=createXMLRequest();
    xmlHttp.onreadystatechange = function() 
    {
    if (xmlHttp.readyState == 4) 
    {
            if (xmlHttp.status < 400) 
            {
                callback(xmlHttp,data);
            }
            else 
            {
                alert("当加载数据时发生错误 :" + xmlHttp.status+ "/" + xmlHttp.statusText);
            }
            //删除XMLHTTP，释放资源
            try {
                delete xmlHttp;
                xmlHttp = null;
            } catch (e) {
				//alert("xmlHttp error");
			}
        }
    }
    //如果以POST方式回发服务器
    if (method=="POST")
     {
        xmlHttp.open("POST", url, true);
        xmlHttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');        
        xmlHttp.send(data);
        Request.reqList.push(xmlHttp);
    }
    //以GET方式请求
    else 
    {
        xmlHttp.open("GET", url, true);
        xmlHttp.send(null);
        Request.reqList.push(xmlHttp);
    }
    return xmlHttp;
}
//全部清除XMLHTTP数组元素，释放资源
Request.clearReqList = function()
{
    var ln = Request.reqList.length;
    for(var i=0; i<ln; i++)
    {
        var xmlHttp = Request.reqList[i];
        if(xmlHttp)
        {
            try{
                delete xmlHttp; 
            } catch (e) {}
        }
    }
    Request.reqList = [];   
}

//进一步封装XMLHTTP以GET方式发送请求时的代码
Request.sendGET = function(url,callback) 
{
    Request.clearReqList();
    return Request.send(url, "GET", callback, null);
}
//进一步封装XMLHTTP以POST方式发送请求时的代码
Request.sendPOST = function(url, callback,data) 
{
    Request.clearReqList();
    Request.send(url, "POST", callback, data);
}