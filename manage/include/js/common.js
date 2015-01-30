var BROWSER = {};
var lang = new Array();
var USERAGENT = navigator.userAgent.toLowerCase();
var is_opera = USERAGENT.indexOf('opera') != -1 && opera.version();
var is_moz = (navigator.product == 'Gecko') && USERAGENT.substr(USERAGENT.indexOf('firefox') + 8, 3);
var is_ie = (USERAGENT.indexOf('msie') != -1 && !is_opera) && USERAGENT.substr(USERAGENT.indexOf('msie') + 5, 3);

BROWSER.ie = window.ActiveXObject && USERAGENT.indexOf('msie') != -1 && USERAGENT.substr(USERAGENT.indexOf('msie') + 5, 3);
BROWSER.firefox = USERAGENT.indexOf('firefox') != -1 && USERAGENT.substr(USERAGENT.indexOf('firefox') + 8, 3);
BROWSER.chrome = window.MessageEvent && !document.getBoxObjectFor && USERAGENT.indexOf('chrome') != -1 && USERAGENT.substr(USERAGENT.indexOf('chrome') + 7, 10);
BROWSER.opera = window.opera && opera.version();
BROWSER.safari = window.openDatabase && USERAGENT.indexOf('safari') != -1 && USERAGENT.substr(USERAGENT.indexOf('safari') + 7, 8);
BROWSER.other = !BROWSER.ie && !BROWSER.firefox && !BROWSER.chrome && !BROWSER.opera && !BROWSER.safari;
BROWSER.firefox = BROWSER.chrome ? 1 : BROWSER.firefox;


function $(id) {
	return document.getElementById(id);
}

Array.prototype.push = function(value) {
	this[this.length] = value;
	return this.length;
}

function isUndefined(variable) {
	return typeof variable == 'undefined' ? true : false;
}

function doane(event) {
	e = event ? event : window.event;
	if(is_ie) {
		e.returnValue = false;
		e.cancelBubble = true;
	} else if(e) {
		e.stopPropagation();
		e.preventDefault();
	}
}


var winleft = 0;
var wintop = 0;
var layernum = 0;
function pmwin(action, param,clayer) {
	var layer = 'layer'+layernum;
	var winmask = 'winmask'+layernum;	

	var showWin = document.getElementsByName("showWin").length;
	
	if(showWin>0) {
		winleft = showWin*40;
		wintop = showWin*20;
	}else{
		winleft = 0;
		wintop = 0;		
	}
	var topheight = 100+wintop;
	var winwidth = 750;
	var objs = document.getElementsByTagName("OBJECT");
	if(action == 'open') {
		for(i = 0;i < objs.length; i ++) {
			if(objs[i].style.visibility != 'hidden') {
				objs[i].setAttribute("oldvisibility", objs[i].style.visibility);
				objs[i].style.visibility = 'hidden';
			}
		}
		var clientWidth = document.body.clientWidth;
		var clientHeight = document.documentElement.clientHeight ? document.documentElement.clientHeight : document.body.clientHeight;
		var scrollTop = document.body.scrollTop ? document.body.scrollTop : document.documentElement.scrollTop;
		var pmwidth = winwidth;
		var pmheight = clientHeight * 0.9;
					

		if(!$(layer)) {
			
			div = document.createElement('div');div.id = layer;
			div.style.width = pmwidth + 'px';
			div.style.height = pmheight + 'px';
			div.style.left = ((clientWidth - pmwidth + winleft) / 2 ) + 'px';
			div.style.position = 'absolute';
			div.style.zIndex = '666';
			$('append_parent').appendChild(div);
			$(layer).innerHTML = '<div class="showWin" style="width: '+winwidth+'px;position: relative;top:'+topheight+'px;">' +
				'<a href="javascript:void(0)" onclick="pmwin(\'close\',\'\',\''+layer+'\');"title="Close" style="position: absolute; right: 15px; top: 12px"><b>×</b></a>' +
				'<input type="hidden" name="showWin" /><div id="'+winmask+'"></div></div><iframe  style="left:0;height:100%;width:770px;margin:100px auto;position:absolute; top:10px; z-index:-1;  border-style:none;filter=\'progid:DXImageTransform.Microsoft.Alpha(style=0,opacity=0)\'"></iframe>';
				
		}
		$(layer).style.display = '';
		$(layer).style.top = ((clientHeight - pmheight) / 2 + scrollTop) + 'px';
		if(!param) {
			$(winmask).innerHTML = 'Error!';
		} else {
			selectAjax(param+'&winid='+winmask,winmask);
		}

		layernum = layernum+1; 

	} else if(action == 'close') {
		
		for(i = 0;i < objs.length; i ++) {
			if(objs[i].attributes['oldvisibility']) {
				objs[i].style.visibility = objs[i].attributes['oldvisibility'].nodeValue;
				objs[i].removeAttribute('oldvisibility');
			}
		}

		$(clayer).style.display = 'none';
		$(clayer).innerHTML = '';
	}

}

function setSubmit(obj,val){
	if(!val)  val =  'Please later...' 
	
	document.getElementById(obj).disabled=true;
	document.getElementById(obj).value=val;	
	
}

	
//---------------------------AJAX-----------------------------------------

var xmlHttps = {};

function createXMLHttpRequest() {
	var xmlHttp;
	
	if (window.ActiveXObject) {
		try {
			xmlHttp=new ActiveXObject("Msxml2.XMLHttp");
		}
		catch (e) {
			try{
				xmlHttp=new ActiveXobject("Microsoft.XMLHttp");
			}
			catch (e) {}
		}
    } 
    else if (window.XMLHttpRequest) {
		xmlHttp = new XMLHttpRequest();
		if (xmlHttp.overrideMimeType) {//设置MIME类别
			xmlHttp.overrideMimeType("text/xml");
		}
    }
	
	return xmlHttp;
}

function selectAjax (url, id, valArr, type, loading){
	var xmlHttp = createXMLHttpRequest();
	
	xmlHttps[id] = xmlHttp;
	
	xmlHttp.onreadystatechange = function() {
		//alert(id);
		if (type == 1) {//判断类型
			$(id).options.length = 0;
			setAjaxShow2(xmlHttp, id);
		}
		else{
			setAjaxShow(xmlHttp, id, loading);
		}
	}
	
	if (valArr) {//判断是否设置传递后台提交参数
		var val = valArr.split("|");//拆分		
		for(var i=0;i<val.length;i++){
			nowValue =  $(val[i]).value			
			if ((nowValue == null) || (nowValue == "")) return;
			url	 += "&"+val[i]+"=" + encodeURI(unescape(nowValue));//赋值URL
		}
		url += "&time="+new Date().getTime();
	}
	
	xmlHttp.open("GET", url, true);
	xmlHttp.send(null);  
}

function setAjaxShow(xmlHttp, id, loading) {
	if (xmlHttp.readyState < 4) {
		if (!loading) {
			$(id).innerHTML='<img src="style/loading.gif" align="absmiddle"> 请稍后...';
		}
	}
	if (xmlHttp.readyState == 4) {
		var response = xmlHttp.responseText;
		$(id).innerHTML = response;
	}
}

function setAjaxShow2(xmlHttp, id) {
    if(xmlHttp.readyState == 4) {
        if(xmlHttp.status == 200) {
			var obj = document.getElementById(id);
			eval(xmlHttp.responseText);
        }
    }
}

function clickNum(obj) {//判断数字合法性

	if(isNaN(obj.value) && !(obj.value=='-')) {
		obj.value = parseFloat(obj.value);
		if (obj.value == 'NaN') {
			obj.value = 0;
		}
	}
}

function combineswitch(showid,numJS,url) {//菜单切换
	for(var i = 1;i <= numJS;i++) {	
		document.getElementById('showJLtr' + i).style.display = 'none';
	}
	if (url) selectAjax(url,'showJL'+showid+'');	
	document.getElementById('showJLtr' + showid).style.display = 'block';


}
//---------------------------全选取消-----------------------------------------

var CheckB = 1;
function CheckAll( _Check,myform,id){
	_Form = document.getElementById(myform);
	for (i=0; i<_Form.elements.length; i++){
	ele = _Form.elements[i];
	if (ele.name == id) ele.checked = CheckB;
	}
	_Check.checked = CheckB;
	CheckB = (CheckB == '0') ? 1 : 0;
}
//---------------------------选择-----------------------------------------

function ontable(obj,type) {
	if (type=='on') {
		//showMenu(id);
		obj.className='td3';
		
	}else if(type=='out'){
		obj.className='td';		
	}
}


function textareasize(obj) {
	var clientWidth = document.body.clientWidth;

	if(obj.style.position == 'absolute') {
		obj.style.left ='';
		obj.className = '';			
		obj.style.position = '';
		obj.style.width = '';
		obj.style.height = '';
		obj.style.height = '';
	} else {
		obj.style.width = BROWSER.ie > 6 || !BROWSER.ie ? '400px' : '400px';
		obj.style.position = 'absolute';
		obj.style.left = ((clientWidth - 400) / 2 ) + 'px';
		obj.className = 'showTextarea';
		obj.style.height = '250px';
	}
}
document.write('<iframe id="gopost" name="gopost" width="0" height="0" frameborder="0"></iframe>');//用户提交目标