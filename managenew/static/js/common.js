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