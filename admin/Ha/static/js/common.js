function commonDictionarySplit(s, esp, vq, eq) {
    var res = {};
    if (!s || typeof(s) != "string") {
        return res;
    }
    if (typeof(esp) != 'string') {
        esp = "&";
    }
    if (typeof(vq) != 'string') {
        vq = "";
    }
    if (typeof(eq) != 'string') {
        eq = "=";
    }
    var l = s.split(vq + esp),
        len = l.length,
        tmp, t = eq + vq,
        p;
    if (vq) {
        tmp = l[len - 1].split(vq);
        l[len - 1] = tmp.slice(0, tmp.length - 1).join(vq);
    }
    for (var i = 0, len; i < len; i++) {
        if (eq) {
            tmp = l[i].split(t);
            if (tmp.length > 1) {
                res[tmp[0]] = tmp.slice(1).join(t);
                continue;
            }
        }
        res[l[i]] = true;
    }
    return res;
}

/**
 * PHP函数date对应的js
 * @param  {[string]} format    如：'Y-m-d H:i:S'
 * @param  {[int]} timestamp 时间戳
 * @return {[string]}           [description]
 */
function date (format, timestamp) {
	if (timestamp <= 0){
		return 0;
	}
    var that = this,
        jsdate, f, formatChr = /\\?([a-z])/gi,
        formatChrCb,
        _pad = function (n, c) {
            if ((n = n + '').length < c) {
                return new Array((++c) - n.length).join('0') + n;
            }
            return n;
        },
        txt_words = ["Sun", "Mon", "Tues", "Wednes", "Thurs", "Fri", "Satur", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    formatChrCb = function (t, s) {
        return f[t] ? f[t]() : s;
    };
    //Fri Aug 16 2013 09:27:05 GMT+0800
    f = {
        d: function () {
            return _pad(f.j(), 2);
        },
        j: function () { // Day of month; 1..31
            return jsdate.getDate();
        },
        m: function () { // Month w/leading 0; 01...12
            return _pad(f.n(), 2);
        },
        n: function () { // Month; 1...12
            return jsdate.getMonth() + 1;
        },
        Y: function () { // Full year; e.g. 1980...2010
            return jsdate.getFullYear();
        },
        H: function () { // 24-Hours w/leading 0; 00..23
            return _pad(f.G(), 2);
        },
        G: function () { // 24-Hours; 0..23
            return jsdate.getHours();
        },
        i: function () { // Minutes w/leading 0; 00..59
            return _pad(jsdate.getMinutes(), 2);
        },
        s: function () { // Seconds w/leading 0; 00..59
            return _pad(jsdate.getSeconds(), 2);
        }
    };
    this.date = function (format, timestamp) {
        that = this;
        jsdate = ((typeof timestamp === 'undefined') ? new Date() : // Not provided
        (timestamp instanceof Date) ? new Date(timestamp) : // JS Date()
        new Date(timestamp * 1000) // UNIX timestamp (auto-convert to int)
        );
        return format.replace(formatChr, formatChrCb);
    };
    return this.date(format, timestamp);
}

function trim (str, charlist) {
    var whitespace, l = 0, i = 0; str += '';
    if (!charlist) {
        whitespace = " \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000";
    } else {
        charlist += '';
        whitespace = charlist.replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '$1');
    }
    l = str.length;
    for (i = 0; i < l; i++) {
        if (whitespace.indexOf(str.charAt(i)) === -1) {
            str = str.substring(i);
            break;
        }
    }
 
    l = str.length;
    for (i = l - 1; i >= 0; i--) {
        if (whitespace.indexOf(str.charAt(i)) === -1) {
            str = str.substring(0, i + 1);
            break;
        }
    }
 
    return whitespace.indexOf(str.charAt(0)) === -1 ? str : '';
}

function stripscript(str) {
    var pattern = new RegExp("[`~!@#$^&*()=|{}':;',\\[\\].<>/?~！@#￥……&*（）&;—|{}【】‘；：”“'。，、？]");
    var rs = "";
    for (var i = 0; i < str.length; i++) {
        rs = rs+str.substr(i, 1).replace(pattern, '');
    }
    return rs;
}

function encodeurl(m,v,c,data) {
    var url = weburl+global_index+'?m='+m+'&c='+c+'&v='+v+data;
    return encodeURIComponent(url);
}



var toggleSpeed = 200;
var easing = 'swing';
var delay = 5000;
var color = 'rgba(255, 238, 150, 1)';
var textColor = '#111';
var handlerNotification = null;
var visibleNotification = false;
var timeout = null;
var can_display_message = true;

function notify(message, type, notification_color) {

    type = typeof(type) != 'undefined' ? type : 'normal';
    notification_color = typeof(notification_color) != 'undefined' ? notification_color : 'info';

    if(notification_color == 'success') {
        color = '#5EB95E';
        textColor = '#fff';
    }
    else if(notification_color == 'error') {
        color = '#F2DEDE';
        textColor = '#B94A48';
    }
    else {
        color = 'rgba(255, 238, 150, 1)';
        textColor = '#111';
    }

    $('.ajax-notification-bar').show();
    
    $('.ajax-notification-bar').append('<p>'+message+'</p>');

    $('.ajax-notification-bar').css('background-color', color);
    $('.ajax-notification-bar p').css('color', textColor);

    $('.ajax-notification-bar p').last().delay(toggleSpeed/2).animate({
        opacity: 1,
        paddingLeft: '+=20'
    }, toggleSpeed/2, easing);
    
    $('.ajax-notification-bar p').last().slideDown(toggleSpeed, easing, function() {

        if(type == 'normal') {
            if(!visibleNotification) {
                clearTimeout(timeout);
                timeout = setTimeout(hideNotification, delay);
            }
            visibleNotification = true;
        }
        else if(type == 'error') {
            clearTimeout(timeout);
        }

        show_notification_close_button();
        
    });

    $('.ajax-notification-bar').unbind('click');
    $('.ajax-notification-bar').click(function() {
        hide_notification_close_button();
        $('.ajax-notification-bar p').slideUp(toggleSpeed, easing, function() {
            $(this).remove();
            $('.ajax-notification-bar').hide();
        });
        visibleNotification = false;
    });
    
}

function hideNotification() {

    if(visibleNotification === true) {
        
        if($('.ajax-notification-bar p').length == 1) {
            hide_notification_close_button();
        }

        $('.ajax-notification-bar p').first().animate({
            opacity: 0
        //paddingLeft: '-=40'
        }, toggleSpeed/2, easing);
        $('.ajax-notification-bar p').first().slideUp(toggleSpeed, easing, function() {
            $('.ajax-notification-bar p').first().remove();
            if($('.ajax-notification-bar p').length === 0) {
                visibleNotification = false;
                clearTimeout(timeout);
                $('.ajax-notification-bar').hide();
            }
            else {
                clearTimeout(timeout);
                timeout = setTimeout(hideNotification, delay/2);
            }
        });

    }

}

function notify_once_every(interval, message) {
    if(can_display_message) {
        notify(message);
        can_display_message = false;
        display_message_timeout =  setTimeout(can_display_message_variable, interval);
    }
}

function can_display_message_variable() {
    can_display_message = true;
}

function show_notification_close_button() {
    $('.ajax-notification-bar .close').show();
    $('.ajax-notification-bar .close').animate({
        opacity: 0.2
    }, toggleSpeed, easing);
}

function hide_notification_close_button() {
    $('.ajax-notification-bar .close').animate({
        opacity: 0
    }, toggleSpeed/3, easing, function() {
        $('.ajax-notification-bar .close').hide();
    });
}

function arrayDel(arrayItems, num){
    for ( var i=0 ; i < arrayItems.length ; ++i ){
        if ( arrayItems[i] == num ){
            if ( i > arrayItems.length/2 ){
                for ( var j=i ; j < arrayItems.length-1 ; ++j ){
                    arrayItems[j] = arrayItems[j+1];
                }
                arrayItems.pop();
        }else{
                for ( var j = i ; j > 0 ; --j ){
                    arrayItems[j] = arrayItems[j-1];
                }
                arrayItems.shift();
            }
            break;
        }
    }
}

function nl2br(str){
    var breakTag = '<br>';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}

function cid_to_name(cid){
    if (global_companylist.length > 0 && cid > 0){
        for(var key in global_companylist){
            if (global_companylist[key].cid == cid){
                return global_companylist[key].name;
            }
        }
    }
}

function sid_to_name(sid) {
    if (sid > 0 && typeof global_serverlist != 'undefined'){
        for (var key in global_serverlist) {
            if (global_serverlist[key].sid == sid) {
                return global_serverlist[key].name + '-' + global_serverlist[key].o_name;
            }
        }
    }
    return '';
}

function sum(plus1, plus2){
    return parseInt(plus1) + parseInt(plus2);
}

function getServerByCid(cid) {
    if (typeof global_serverlist == 'undefined') {
        return false;
    }
    if (global_serverlist.length < 1) {
        return false;
    }
    var server = [];
    for (var key in global_serverlist) {
        if (global_serverlist[key].cid == cid) {
            server.push(global_serverlist[key]);
        }
    }
    return server;
}

//加载二级、三级菜单
function load_menu(mid, url, cpp){
    if (mid <= 0) return false;
    var obj = $('a[data-mid="'+mid+'"]', $('#mainmenu'));
    if (obj.is('a')){
        $('li.current', $('#mainmenu')).removeClass();
        obj.parent('li').addClass('current');
    }

    if ($('#submenu').is(':hidden')){
        $('#submenu').fadeIn();
    }

    if (cpp != undefined && cpp > 0){
        setTimeout(function(){
            var cobj = $('a[data-mid="'+cpp+'"]', $('#submenu'));
            if (cobj.is('a')){
                if ($('.current', $('#submenu')).attr('data-mid') != cpp) {
                    $('.current', $('#submenu')).removeClass();
                    cobj.parent().addClass('current');
                }
            }
        }, 250);
    }

    if (typeof submenujson[mid] != 'undefined' && submenujson[mid] !== false) {
        $('#submenu').html($( "#submenutpl").tmpl( submenujson[mid] ));
        $('#sidebar').show();
    }else {
        $('#sidebar').hide();
    }

    if (url != undefined && url != ''){
        url = decodeURIComponent(url);

        if (url.indexOf('http://') == 0 || url.indexOf(global_index) == 0){
            $('#wrap_common').removeClass().addClass('wrapper cf');
            mod.load(url,function(response,status){});
            $('#page_title').hide();
        }
    }else{
        if (mid == 1) {
            $('#wrap_common').removeClass().addClass('dashboard cf');
            mod.load(global_index+'?m=manage&c=index&v=main',function(response,status){});
        }else if (mid > 6 && mid != 120) {
            url = $('a[data-mid="'+cpp+'"]', $('#submenu')).attr('rel');
            if (url != ''){
                $('#wrap_common').removeClass().addClass('wrapper cf');
                mod.load(url,function(response,status){});
                $('#page_title').hide();
            }
        }else {
            $('#submenu').css('top', 'auto');
            if (typeof submenujson[mid][0].thirdmenu != 'undefined') {
                if (submenujson[mid][0].thirdmenu != false){
                    cpp = submenujson[mid][0].thirdmenu[0].mid;
                }else {
                    cpp = submenujson[mid][0].mid;
                }
            }else {
                cpp = submenujson[mid][0].mid;
            }
            url = $('a[data-mid="'+cpp+'"]', $('#submenu')).attr('rel');
            if (url != ''){
                $('#wrap_common').removeClass().addClass('wrapper cf');
                mod.load(url,function(response,status){});
                var cobj = $('a[data-mid="'+cpp+'"]', $('#submenu'));
                if (cobj.is('a')){
                    if ($('.current', $('#submenu')).attr('data-mid') != cpp) {
                        $('.current', $('#submenu')).removeClass();
                        cobj.parent().addClass('current');
                    }
                }
                $('#page_title').hide();
            }
        }
    }
    return false;
}

function pageload(hash){
    if (hash) {
        var r=/(?:Firefox|GranParadiso|Iceweasel|Minefield).(\d+\.\d+)/i;
        var isfirefox = parseFloat((r.exec(navigator.userAgent)||r.exec('Firefox/3.3'))[1],10);
        var t, hs= isfirefox ? ((t=location.href.split("#"))&&t[1]?("#"+t[1]):''):hash;
        if (!-[1,]) {
            hs = decodeURIComponent(hs);
        }
        if((t=hs.indexOf("#"))==0){
            hs = hs.substring(1);
            t = commonDictionarySplit(hs, '&');
            if (t.app > 0){
                if (t.app < 7 && typeof t.cpp == 'undefined') {
                    //t.url = $('a[data-mid="'+t.app+'"]', $('#mainmenu')).attr('rel');
                }else if (typeof t.cpp != 'undefined' && !-[1,]) {
                    if (submenujson[t.app] != undefined && submenujson[t.app] != false && typeof t.url == 'undefined'){
                        var ttempjson;
                        var tempjson = submenujson[t.app];
                        for (var nkey in tempjson){
                            if (tempjson[nkey].mid == t.cpp) {
                                t.url = encodeurl(tempjson[nkey].m, tempjson[nkey].v, tempjson[nkey].c, tempjson[nkey].data);
                                break;
                            }

                            if (tempjson[nkey].thirdmenu != false){
                                ttempjson = tempjson[nkey].thirdmenu;
                                for (var tkey in ttempjson){
                                    if (ttempjson[tkey].mid == t.cpp) {
                                        t.url = encodeurl(ttempjson[tkey].m, ttempjson[tkey].v, ttempjson[tkey].c, ttempjson[tkey].data);
                                        break;
                                    }
                                }
                            }
                        }
                    }else if (typeof t.url != 'undefined') {
                        var urlarr = new Array();
                        for (var ukey in t) {
                            if (ukey != 'app' && ukey != 'cpp' && ukey != 'url')    urlarr.push(ukey+'='+t[ukey]);
                        }
                        t.url = t.url + '&' + urlarr.join('&');
                    }
                }
                mod.html('');
                Ha.common.loadImg('module');
                load_menu(t.app, t.url, t.cpp);
                $('html,body').animate({scrollTop: '0px'}, 800);
            }
        }else {
            mod.html('');
            Ha.common.loadImg('module');
            load_menu(default_menu);
            $('html,body').animate({scrollTop: '0px'}, 800);
        }
    }
    return false;
}
function setSubmit(obj,val){
	if(!val)  val =  '请稍后...' 
	
	document.getElementById(obj).disabled=true;
	document.getElementById(obj).value=val;	
	
}