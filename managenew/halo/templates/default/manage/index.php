<?php defined('IN_G') or exit('No permission resources.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--[if IE 7]><meta http-equiv="X-UA-Compatible" content="IE=7" /><![endif]-->
<!--[if lte IE 6]><meta http-equiv="X-UA-Compatible" content="IE=6" /><![endif]-->
<title><?php echo Lang('title'); ?></title>
<link type="text/css" rel="stylesheet" href="static/css/main.css" />
<link type="text/css" rel="stylesheet" href="static/css/blue.css" />
<link type="text/css" rel="stylesheet" href="static/css/zTreeStyle.css">
</head>
<body>
<div class="menuW">
	<div class="menuIn">
		<div class="menu">
			<div class="wrap clearfix">
				<div class="menuL" id="menuL">
					<ul id="mainmenu">

					</ul>
				</div>
				<div class="tempsearch">
					<input type="text" value="<?php echo Lang('search_function'); ?>" name="keyword" class="input" id="searchinput">
					<a href="javascript:;" class="btn" id="btn_headersearch"></a>
					<ul class="dropdown-menu followsearch" id="followsearch" style="top:30px;left:0px;display:none;"></ul>
				</div>
				<p class="tempLink" id="loading" style="display:none;">
					<a><img alt="Loading..." src="static/images/loading.gif" width="20px" height="20px"></a>
				</p>
				<p class="menuR">
					<a><?php echo param::get_cookie('username')?></a>
					<a href="#app=2&url=<?php echo urlencode(WEB_URL.INDEX.'?m=manage&c=user&v=edit_password') ?>" class="c_txt" rel="<?php echo INDEX; ?>?m=manage&c=user&v=edit_password"><?php echo Lang('edit_password') ?></a>|<a href="<?php echo INDEX; ?>?m=manage&c=index&v=logout"><?php echo Lang('logout') ?></a>
				</p>
			</div>
		</div>
	</div>
</div>

<div id="stretch" style="position: fixed;width:30px;height:20px;left:88px;margin-top:10px;background: #F05537;border: 1px solid #EA3E26;border-right:0;text-align:center;z-index: 1000;">
	<a href="javascript:;" style="width:30px;font-size:20px;color:white;line-height:18px;" title="展开与关闭"> < </a>
</div>
<div class="leftmenu" id="submenu">

</div>
<div id="scrolllink">
	<span id="menuscrollup"><img src="static/images/scrollu.png"></span><span id="menuscrolldown"><img src="static/images/scrolld.png"></span>
</div>
<!-- 中间 -->
<div id="container">
<div id="bgwrap">
<br class="clear">
<p>
正在加载中...
</p>
</div>
</div>
<br>
<br>

<div style="width: 100%; ">
	<div style="position: fixed; left: 120pt;z-index: 1001;bottom: 0px;width:100%">
		<div class="bd"  style="bottom: 0px; ">
			<div class="favguidelist">
				<ul id="quickguidelist">
					<li><a href="javascript:;" class="poptips_btn" id="add_quick" title="添加快捷导航"><span style="color:#F60">╋添加</span></a></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<div id="fixLayout" style="width: 100%; ">
	<div style="bottom: 0px; right: 0px; z-index: 1001; width: 100%; position: fixed; ">
		<div class="gb_poptips" style="bottom: 0px; ">
			<div class="gb_poptips_btn">
				<a title="" href="javascript:void(0);" class="poptips_btn" id="returnTop" title="回到顶部"><i class="ui_icon icon_ticker_top" title="回到顶部"></i></a>
			</div>
		</div>
	</div>
</div>
<script type="text/template" id="mainmenutpl">
{{if mid == 1}}
	<li class="on"><a href="./<?php echo INDEX; ?>"><span>${mname}</span></a></li>
{{else islink == 1}}
	<li><a href="${urllink}" target="_blank"><span>${mname}</span></a></li>
{{else}}
	<li><a href="#app=${mid}" data-mid="${mid}" rel="<?php echo INDEX; ?>?m=${m}&c=${c}&v=${v}${data}" class="a_txt"><span>${mname}</span></a></li>
{{/if}}
</script>

<script type="text/template" id="submenutpl">
{{if typeof(thirdmenu) != 'undefined' && thirdmenu != false}}

	{{if islink == 1}}
    <div class="mainTitle"><h4><a href="${urllink}" target="_blank" class="lBlue"><span>${mname}</span></a></h4></div>
    {{else}}
    <div class="mainTitle"><h4><a href="javascript:;" class="lBlue slide" data-mid="${mid}"><span>${mname}︿</span></a></h4></div>
    {{/if}}

    <ul class="affairs" id="slide_${mid}">
    	{{each thirdmenu}}
    		{{if islink == 1}}
    		<li><a href="${urllink}" target="_blank"><span>${mname}</span></a></li>
    		{{else}}
    		<li><a href="#app=${parentpath.substring(1,2)}&cpp=${mid}&url=${encodeurl(m, v, c, data)}" rel="<?php echo INDEX; ?>?m=${m}&c=${c}&v=${v}${data}" data-mid="${mid}" class="b_txt"><span>${mname}</span></a></li>
    		{{/if}}
		{{/each}}
    </ul>
{{else}}

        {{if islink == 1}}
        <div class="mainTitle"><h4><a href="${urllink}" target="_blank" class="lBlue"><span>${mname}</span></a></h4></div>
        {{else}}
        <div class="mainTitle"><h4><a href="#app=${parentid}&cpp=${mid}&url=${encodeurl(m, v, c, data)}" rel="<?php echo INDEX; ?>?m=${m}&c=${c}&v=${v}${data}" data-mid="${mid}" class="lBlue b_txt"><span>${mname}</span></a></h4></div>
        {{/if}}
        
{{/if}}
</script>

<script type="text/template" id="quickguidetpl">
<li><a href="${qurl}" class="poptips_btn qview"><span>[${qname}]</span></a><a href="javascript:;" class="poptips_btn qdelete" data-id="${qid}"><span>×</span></a></li>
</script>

<script type="text/template" id="global_companylisttpl">
<option value="${cid}">${fn} - ${name}</option>
</script>

<script type="text/template" id="global_serverlisttpl">
<option value="${sid}" data-ver="${server_ver}">${name}-${o_name}</option>
</script>

<!-- 头部 -->
<script type="text/javascript" src="static/js/jquery.min.js"></script>
<script type="text/javascript" src="static/js/jquery.tmpl.min.js"></script>
<script type="text/javascript" src="static/js/jquery.pager.js"></script>
<script type="text/javascript" src="static/js/jquery.artDialog.min.js"></script>
<script type="text/javascript" src="static/js/artDialog.plugins.min.js"></script>
<script type="text/javascript" src="static/js/common.js"></script>
<script type="text/javascript" src="static/js/jquery.mousewheel.js"></script>
<script type="text/javascript" src="static/js/jquery.history.js"></script>

<script type="text/javascript">
var weburl = '<?php echo WEB_URL ?>', 
	global_index = '<?php echo INDEX; ?>';
var menujson = <?php echo $data['menu']['main'] ? $data['menu']['main'] : '{}'; ?>, 
    submenujson = <?php echo $data['menu']['sub'] ? $data['menu']['sub'] : '{}'; ?>;
var pageword = {
	first: '<?php echo Lang('page_first'); ?>', 
	prev: '<?php echo Lang('page_prev'); ?>', 
	next: '<?php echo Lang('page_next'); ?>', 
	last: '<?php echo Lang('page_last'); ?>'
};
var quickguide = <?php echo $data['quicklist'] ? $data['quicklist'] : '[]' ?>;
var global_companylist = [], global_serverlist = [];

//加载二级、三级菜单
function load_menu(mid, url, cpp){
	if (mid <= 0) return false;
	var obj = $('a[data-mid="'+mid+'"]', $('#mainmenu'));
	if (obj.is('a')){
		$('li.on', $('#mainmenu')).removeClass();
		obj.parent('li').addClass('on');
	}

	if ($('#submenu').is(':hidden')){
		$('#submenu').fadeIn();
		$('#scrolllink').fadeIn();
		$('#stretch').children().html(' < ');
		$('#stretch').css('left', '88px');
		$('#container').css('margin-left', '120px');
	}

	if (cpp != undefined && cpp > 0){
		setTimeout(function(){
			var cobj = $('a[data-mid="'+cpp+'"]', $('#submenu'));
			if (cobj.is('a')){
				if ($('.on', $('#submenu')).attr('data-mid') != cpp) {
					$('.on', $('#submenu')).removeClass();
					cobj.parent().addClass('on');
				}
			}
		}, 250);
	}

	if (typeof submenujson[mid] != 'undefined' && submenujson[mid] !== false) {
		$('#submenu').html($( "#submenutpl").tmpl( submenujson[mid] ));
		if ($('#scrolllink').is(':hidden')){
			$('#scrolllink').show();
		}
	}else {
		$('#scrolllink').hide();
	}

	var loadingHtml = '<div id="content_loading" style="position:absolute;top:0;left:0;width:100%;height:100%;z-index:1100;text-align:center;"><div style="width:100%;height:100%;background:#fff;opacity:0.5;filter:alpha(opacity=50);"></div><img style="position:absolute;top:10px;left:45%;" src="static/images/content_loading.gif"></div>';

	if (url != undefined && url != ''){
		url = decodeURIComponent(url);

		if (url.indexOf('http://') == 0 || url.indexOf('<?php echo INDEX; ?>') == 0){
			$(loadingHtml).appendTo('#container');
			$('#container').load(url,function(response,status){});
		}
	}else{
		if (mid == 1) {
			$(loadingHtml).appendTo('#container');
			$('#container').load('<?php echo INDEX; ?>?m=manage&c=index&v=main',function(response,status){});
		}else if (mid > 6) {
			url = $('a[data-mid="'+cpp+'"]', $('#submenu')).attr('rel');
			if (url != ''){
				$(loadingHtml).appendTo('#container');
				$('#container').load(url,function(response,status){});
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
                $(loadingHtml).appendTo('#container');
                $('#container').load(url,function(response,status){});
				var cobj = $('a[data-mid="'+cpp+'"]', $('#submenu'));
    	        if (cobj.is('a')){
	                if ($('.on', $('#submenu')).attr('data-mid') != cpp) {
                    	$('.on', $('#submenu')).removeClass();
                    	cobj.parent().addClass('on');
                	}
            	}
            }
		}
	}

	return false;
}

function searchMenu() {
	var word = '';
	var readHtml = '';
	var readLen = 0;
	var keyword = $.trim($('#searchinput').val());
	var pattern = new RegExp("[`~!@#$^&*()=|{}':;',\\[\\].<>/?~！@#￥……&*（）&;—|{}【】‘；：”“'。，、？]");
	var readjson = [];
	var curjson;
	var tempjson;
	var ttempjson;
	$('#followsearch').empty();
	for (var i=0; i<keyword.length; i++) {
		if (readLen >= 10) {
			break;
		}
		word = keyword.substr(i, 1);
		if (pattern.test(word)) {
			continue;
		}

		if (menujson.length > 0){
			for (var key in menujson){
				curjson = menujson[key];
				if (readLen < 10 && curjson.mname.indexOf(word) >= 0 && $('#search_'+curjson.mid).html() == null){
					readLen++;
					$('#followsearch').append('<li id="search_'+curjson.mid+'"><a href="javascript:;" tabindex="-1" class="s_txt" mid="'+curjson.mid+'" pid="0">'+curjson.mname+'</a></li>');
				}
				if (submenujson[curjson.mid] != undefined && submenujson[curjson.mid] != false){
					tempjson = submenujson[curjson.mid];
					for (var nkey in tempjson){
						if (readLen < 10 && tempjson[nkey].thirdmenu == false && tempjson[nkey].mname.indexOf(word) >= 0 && $('#search_'+tempjson[nkey].mid).html() == null){
							readLen++;
							$('#followsearch').append('<li id="search_'+tempjson[nkey].mid+'"><a href="javascript:;" class="s_txt" mid="'+tempjson[nkey].mid+'" pid="'+curjson.mid+'">'+tempjson[nkey].mname+'</a></li>');
						}

						if (tempjson[nkey].thirdmenu != false){
							ttempjson = tempjson[nkey].thirdmenu;
							for (var tkey in ttempjson){
								if (readLen < 10 && ttempjson[tkey].mname.indexOf(word) >= 0 && $('#search_'+ttempjson[tkey].mid).html() == null){
									readLen++;
									$('#followsearch').append('<li id="search_'+ttempjson[tkey].mid+'"><a href="javascript:;" class="s_txt" mid="'+ttempjson[tkey].mid+'" pid="'+curjson.mid+'">'+ttempjson[tkey].mname+'</a></li>');
								}
							}
						}
					}
				}
			}
		}
	}
	$('#followsearch').show();
	readLen = 0;
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
							if (ukey != 'app' && ukey != 'cpp' && ukey != 'url')	urlarr.push(ukey+'='+t[ukey]);
						}
						t.url = t.url + '&' + urlarr.join('&');
					}
				}
				load_menu(t.app, t.url, t.cpp);
			}
		}else {
			load_menu(<?php echo $data['default'];?>);
		}
    }
	return false;
}
$(function(){
	$( "#mainmenutpl" ).tmpl( menujson ).prependTo( "#mainmenu" );
	$.history.init(pageload);
	//默认加载
	var r=/(?:Firefox|GranParadiso|Iceweasel|Minefield).(\d+\.\d+)/i;
	var isfirefox = parseFloat((r.exec(navigator.userAgent)||r.exec('Firefox/3.3'))[1],10);
	var t, hs= isfirefox ? ((t=location.href.split("#"))&&t[1]?("#"+t[1]):''):location.hash;
	if((t=hs.indexOf("#")) == -1){
		load_menu(<?php echo $data['default'];?>);
	}

	$('#submenu').on('click', 'a.b_txt', function(){
		var loadurl = $(this).attr('rel'), loadmid = $(this).attr('data-mid');
		$('.on', $('#submenu')).removeClass('on');
		$(this).parent().addClass('on');
	});

	$('#followsearch').on('click', 'a.s_txt', function(){
		var cpp = $(this).attr('mid'), mid = $(this).attr('pid');
		if (mid == 0){
			mid = cpp;
			cpp = 0;
			load_menu(mid, '', cpp);
			return ;
		}
		if (typeof submenujson[mid] != 'undefined' && submenujson[mid] != false) {
			var searchmenu = submenujson[mid];
			var thirdmenu;
			var hash = '';
			for(var key in searchmenu) {
				if (searchmenu[key].mid == cpp) {
					hash = 'app='+mid+'&cpp='+cpp+'&url='+encodeurl(searchmenu[key].m, searchmenu[key].v, searchmenu[key].c, searchmenu[key].data);
				}
				if (searchmenu[key].thirdmenu != false) {
					thirdmenu = searchmenu[key].thirdmenu;
					for(var tkey in thirdmenu) {
						if (thirdmenu[tkey].mid == cpp) {
							hash = 'app='+mid+'&cpp='+cpp+'&url='+encodeurl(thirdmenu[tkey].m, thirdmenu[tkey].v, thirdmenu[tkey].c, thirdmenu[tkey].data);
							break;
						}
					}
				}
				if (hash != '') {
					location.hash = hash;
					break;
				}
			}
		}
	});
	$('tbody[id$="list"] tr').live({
		mouseover: function(){
			$(this).addClass('ruled');
		},
		mouseout: function(){
			$(this).removeClass('ruled');
		}
	});

	$('#searchinput').on({
		keydown: function(e) {
			if (!/(38|40|27|13)/.test(e.keyCode)) {
				return ;
			}
			if (e.keyCode == 13) {
				searchMenu();
			}else {
				var items, index;
				e.preventDefault();
				e.stopPropagation();
				items = $('#followsearch a');
				if (!items.length) return;
				index = items.index(items.filter(':focus'))

				if (e.keyCode == 38 && index > 0) index--;
				if (e.keyCode == 40 && index < items.length - 1) index++;
				if (!~index) index = 0;

				items.eq(index).focus();
			}
		},
		blur: function(){
			if ($(this).val() == ''){
				$(this).val('<?php echo Lang('search_function'); ?>');
			}
		},
		focus: function(){
			if ($(this).val() == '<?php echo Lang('search_function'); ?>'){
				$(this).val('');
			}
		}
	});

	$('#followsearch').on('keydown', function(e) {
		if (!/(38|40|27)/.test(e.keyCode)) {
			return ;
		}
		var items, index;
		e.preventDefault();
		e.stopPropagation();
		items = $('#followsearch a');
		if (!items.length) return;
		index = items.index(items.filter(':focus'))

		if (e.keyCode == 38 && index > 0) index--;
		if (e.keyCode == 40 && index < items.length - 1) index++;
		if (!~index) index = 0;

		items.eq(index).focus();
	});

	$('#btn_headersearch').on('click', function(e) {
		e.preventDefault()
		e.stopPropagation()
		searchMenu();
	});

	$('body').on('click', function() {
		$('#followsearch').fadeOut();
	});

	$('#submenu').on('click', 'a.slide', function(){
		var aobj = $(this), mid = aobj.attr('data-mid'), atxt = aobj.children('span').html();
		if (mid > 0){
			$('#slide_'+mid).slideToggle('normal', function(){
				if ($(this).is(':hidden')){
					atxt = atxt.replace(/︿/, '﹀');
				}else {
					atxt = atxt.replace(/﹀/, '︿');
				}
				aobj.children('span').html(atxt);
			});
		}
	});

	$('#loading').ajaxStart(function(){
		$(this).show();
	});
	$('#loading').ajaxStop(function(){
		$(this).hide();
	});
	$("#loading").ajaxComplete(function(){
		$(this).hide();
	});

	$("a").live("focus",function(){this.blur()}); 

	$('#quickguidetpl').tmpl(quickguide).prependTo('#quickguidelist');
	/**
	 * 回顶部
	 * @return {[type]} [description]
	 */
	$('#returnTop').on('click', function(){
		$('html,body').animate({scrollTop: '0px'}, 800);
	});

	var inttop = 0;
	$('#submenu').mousewheel(function(event, delta, deltaX, deltaY) {
        if (($('#submenu').css('top') == 'auto' || $('#submenu').css('top') >= '0') && delta > 0){
 
		}else {
			inttop += delta;
			if (inttop == 0){
				$('#submenu').css('top', 'auto');
			}else {
				$('#submenu').css('top', inttop*25+'px');
			}
		}
		event.stopPropagation();
		event.preventDefault();
	});
	/**
	 * 添加快捷方式
	 * @return {[type]} [description]
	 */
	var qdialog;
	$('#add_quick').on('click', function(){
		var curl = window.location;
		var strHtml = [
			'<form id="quick_post_submit" method="post" action="<?php echo INDEX; ?>?m=manage&c=quickguide&v=setting">',
			'<table class="global" style="min-width:600px;width:600px">',
			'<tr>',
			'<th style="width:20%">当前链接：</th>',
			'<td><input	type="text" name="qurl" value="'+curl+'" readonly  style="width:60%"/></td>',
			'</tr>',
			'<tr>',
			'<th>快捷方式名称：</th>',
			'<td><input	type="text" name="qname" style="width:60%" value="'+$('ul.dash1').find('.selected span').html()+'" /></td>',
			'</tr>',
			'<tr>',
			'<th>&nbsp;</th>',
			'<td>',
			'<input type="hidden" name="doSubmit" value="1">',
			'<input type="submit" id="quick_btnsubmit" class="button" value="<?php echo Lang("save"); ?>"></td>',
			'</tr>',
			'</table>',
			'</form>'
		].join('');
		qdialog = $.dialog({id: 'quick', title: '添加快捷方式', width: 500});
		qdialog.content(strHtml);
	});
	$('#quick_post_submit').live('submit', function(e){
		e.preventDefault();
		var objform = $(this), qurl = $.trim(objform.find('input[name="qurl"]').val()), qname = $.trim(objform.find('input[name="qname"]').val());
		if (qurl != '' && qname != ''){
			$.ajax({
				url: '<?php echo INDEX; ?>?m=manage&c=quickguide&v=setting',
				type: 'post',
				data: objform.serialize(),
				dataType: 'json',
				success: function(data){
					if (data.status == 0){
						$('#quickguidetpl').tmpl(data.info).insertBefore($('#add_quick').parent());
						qdialog.close();
					}else {
						qdialog.close();
						alert(data.msg);
					}
				}
			});
		}
	});

	/**
	 * 快捷导航
	 * @return {[type]} [description]
	 */
	$('#quickguidelist').on('click', 'a.qview', function(){
		var qurl = $(this).attr('href');
		if (qurl.indexOf("#") >= 0) {
			var reg = new RegExp(weburl, 'g');
			qurl = qurl.replace(reg, '');
			qurl = qurl.substring(1);
			var t = commonDictionarySplit(qurl);
			if (t.app > 0){
				load_menu(t.app, t.url, t.cpp);
			}
		}else if (qurl.indexOf("http://") == 0){
			location.href = qurl;
		}else {
			return false;
		}
	});

	/**
	 * 删除快捷导航
	 * 
	 */
	$('#quickguidelist').on('click', 'a.qdelete', function(){
		var obj = $(this), qid = obj.attr('data-id');
		if (qid > 0){
			$.ajax({
				url: '<?php echo INDEX; ?>?m=manage&c=quickguide&v=delete',
				data: {qid: qid},
				dataType: 'json',
				success: function(data){
					if (data.status == 0){
						obj.parent().remove();	
					}
				}
			});
		}
	});

	$('#menuscrollup').on('click', function(event) {
		if ($('#submenu').css('top') == 'auto' || $('#submenu').css('top') >= '0'){

		}else {
			inttop += 1;
			if (inttop == 0){
				$('#submenu').css('top', 'auto');
			}else {
				$('#submenu').css('top', inttop*25+'px');
			}
		}
		event.stopPropagation();
		event.preventDefault();
	});
	$('#menuscrolldown').on('click', function(event) {
		inttop -= 1;
		if (inttop == 0){
			$('#submenu').css('top', 'auto');
		}else {
			$('#submenu').css('top', inttop*25+'px');
		}
		event.stopPropagation();
		event.preventDefault();
	});

	$('#stretch').on('click', 'a', function() {
		if ($('#submenu').is(':hidden')) {
			$('#submenu').fadeIn();
			$('#scrolllink').fadeIn();
			$(this).html(' < ');
			$(this).parent().css('left', '88px');
			$('#container').css('margin-left', '120px');
		}else {
			$('#submenu').fadeOut();
			$('#scrolllink').fadeOut();
			$(this).html(' > ');
			$(this).parent().css('left', '0');
			$('#container').css('margin-left', '10px');
		}
	});
});
global_companylist = <?php echo $companylist ? $companylist : '[]'; ?>;
global_serverlist = <?php echo $serverlist ? $serverlist : '[]'; ?>;
</script>
<script type="text/javascript" src="static/js/WdatePicker.js"></script>
</body>
</html>
