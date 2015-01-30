<?php defined('IN_G') or exit('No permission resources.'); ?>
</div>
<br>
<br>
<div style="width: 100%; ">
	<div style="position: fixed; left: 120pt;z-index: 1001;bottom: 0px;width:100%">
		<div class="bd"  style="bottom: 0px; ">
			<div class="favguidelist">
				<ul id="quickguidelist">
					<li><a href="javascript:;" class="poptips_btn" id="add_quick"><span style="color:#F60">╋<?php echo Lang('add')?></span></a></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<div id="fixLayout" style="width: 100%; ">
	<div style="bottom: 0px; right: 0px; z-index: 1001; width: 100%; position: fixed; ">
		<div class="gb_poptips" style="bottom: 0px; ">
			<div class="gb_poptips_btn">
				<a title="" href="javascript:void(0);" class="poptips_btn" id="returnTop" title="<?php echo Lang('back_to_top')?>"><i class="ui_icon icon_ticker_top" title="<?php echo Lang('back_to_top')?>"></i></a>
			</div>
		</div>
	</div>
</div>



<script type="text/template" id="mainmenutpl">
{{if mid == 1}}
	<li class="on"><a href="./"><span>${mname}</span></a></li>
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

<script type="text/template" id="companylisttpl">
<option value="${cid}">${fn} - ${name}</option>
</script>

<script type="text/template" id="serverlisttpl">
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

<script type="text/javascript">
var weburl = '<?php echo WEB_URL ?>', 
    menujson = <?php echo $data['menu']['main'] ? $data['menu']['main'] : '{}'; ?>, 
    submenujson = <?php echo $data['menu']['sub'] ? $data['menu']['sub'] : '{}'; ?>;
var pageword = {
	first: '<?php echo Lang('page_first'); ?>', 
	prev: '<?php echo Lang('page_prev'); ?>', 
	next: '<?php echo Lang('page_next'); ?>', 
	last: '<?php echo Lang('page_last'); ?>'
};
var quickguide = <?php echo $data['quicklist'] ? $data['quicklist'] : '[]' ?>;
var companylist = [], serverlist = [];

//加载二级、三级菜单
function load_menu(mid, url, cpp){
	if (mid <= 0) return false;
	var obj = $('a[data-mid="'+mid+'"]', $('#mainmenu'));
	if (obj.is('a')){
		$('li.on', $('#mainmenu')).removeClass();
		obj.parent('li').addClass('on');
	}

	if (submenujson[mid] == undefined || submenujson[mid] == false){
		$('#submenu').hide();
		return false;
	}

	if ($('#submenu').is(':hidden')){
		$('#submenu').show();
	}

	if (cpp != undefined && cpp > 0){
		setTimeout(function(){
			var cobj = $('a[data-mid="'+cpp+'"]', $('#submenu'));
			if (cobj.is('a')){
				$('.on', $('#submenu')).removeClass();
				cobj.parent().addClass('on');
			}
		}, 300);
	}

	$('#submenu').html($( "#submenutpl").tmpl( submenujson[mid] ));

	if (url != undefined && url != ''){
		url = decodeURIComponent(url);

		if (url.indexOf('http://') == 0 || url.indexOf('<?php echo INDEX; ?>') == 0){
			$('#container').load(url,function(response,status){});
		}
	}else{
		if (mid <= 6){
			url = $('a[data-mid="'+mid+'"]', $('#mainmenu')).attr('rel');
			if (url != ''){
				$('#container').load(url,function(response,status){});
			}
		}else {
			url = $('a[data-mid="'+cpp+'"]', $('#submenu')).attr('rel');
			if (url != ''){
				$('#container').load(url,function(response,status){});
			}
		}
	}
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
	for (var i=0; i<keyword.length; i++){
		if (readLen >= 10){
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
					$('#followsearch').append('<p id="search_'+curjson.mid+'"><a href="javascript:;" class="lBlue s_txt" mid="'+curjson.mid+'" pid="0">'+curjson.mname+'</a></p>');
				}

				if (submenujson[curjson.mid] != undefined && submenujson[curjson.mid] != false){
					tempjson = submenujson[curjson.mid];
					for (var nkey in tempjson){
						if (readLen < 10 && tempjson[nkey].thirdmenu == false && tempjson[nkey].mname.indexOf(word) >= 0 && $('#search_'+tempjson[nkey].mid).html() == null){
							readLen++;
							$('#followsearch').append('<p id="search_'+tempjson[nkey].mid+'"><a href="javascript:;" class="lBlue s_txt" mid="'+tempjson[nkey].mid+'" pid="'+curjson.mid+'">'+tempjson[nkey].mname+'</a></p>');
						}

						if (tempjson[nkey].thirdmenu != false){
							ttempjson = tempjson[nkey].thirdmenu;
							for (var tkey in ttempjson){
								if (readLen < 10 && ttempjson[tkey].mname.indexOf(word) >= 0 && $('#search_'+ttempjson[tkey].mid).html() == null){
									readLen++;
									$('#followsearch').append('<p id="search_'+ttempjson[tkey].mid+'"><a href="javascript:;" class="lBlue s_txt" mid="'+ttempjson[tkey].mid+'" pid="'+curjson.mid+'">'+ttempjson[tkey].mname+'</a></p>');
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
	if (typeof serverlist == 'undefined') {
		return false;
	}
	if (serverlist.length < 1) {
		return false;
	}
	var server = [];
	for (var key in serverlist) {
		if (serverlist[key].cid == cid) {
			server.push(serverlist[key]);
		}
	}
	return server;
}

$(function(){
	$( "#mainmenutpl" ).tmpl( menujson ).prependTo( "#mainmenu" );

	var r=/(?:Firefox|GranParadiso|Iceweasel|Minefield).(\d+\.\d+)/i;
	var isfirefox = parseFloat((r.exec(navigator.userAgent)||r.exec('Firefox/3.3'))[1],10);
	var t, hs= isfirefox ? ((t=location.href.split("#"))&&t[1]?("#"+t[1]):''):location.hash;

	if((t=hs.indexOf("#"))==0){
		hs = hs.substring(2);
		t = commonDictionarySplit(hs, '&');
		if (t.app > 0){
			load_menu(t.app, t.url, t.cpp);
		}
	}else {
		load_menu(1);
	}

	$('#mainmenu').on('click', 'a.a_txt', function(){
		var loadurl = $(this).attr('rel'), loadmid = $(this).attr('data-mid');
		load_menu(loadmid, loadurl);
	});
	$('#submenu').on('click', 'a.b_txt', function(){
		var loadurl = $(this).attr('rel'), loadmid = $(this).attr('data-mid');
		$('.on', $('#submenu')).removeClass('on');
		$(this).parent().addClass('on');
		$('#container').load(loadurl,function(response,status){});
	});
	$('.menuR').on('click', 'a.c_txt', function(){
		var loadurl = $(this).attr('rel');
		$('#container').load(loadurl,function(response,status){});
	});
	$('#followsearch').on('click', 'a.s_txt', function(){
		var cpp = $(this).attr('mid'), mid = $(this).attr('pid');
		if (mid == 0){
			mid = cpp;
			cpp = 0;
		}
		load_menu(mid, '', cpp);
	});
	$('tbody[id$="list"] tr').live({
		mouseover: function(){
			$(this).addClass('ruled');
		},
		mouseout: function(){
			$(this).removeClass('ruled');
		}
		// click: function(){
		// 	if ($(this).hasClass('selected')){
		// 		$(this).removeClass('selected');
		// 	}else {
		// 		$(this).addClass('selected');
		// 	}
		// }
	});

	$('#searchinput').on({
		keyup: function(e) {
			if (e.keyCode != 13){
				return false;
			}
			searchMenu();
		},
		blur: function(){
			if ($(this).val() == ''){
				$(this).val('找功能');
			}
		},
		focus: function(){
			if ($(this).val() == '找功能'){
				$(this).val('');
			}
		}
	});

	$('body').on({
		click: function(){
			$('#followsearch').fadeOut();
		}
	})

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

	$('a', $('.locLeft')).live('click', function(){
		var obj = $(this), hss = obj.attr('href'), t;
		hss = ((t=hss.split("#"))&&t[1]?("#"+t[1]):'');
		if (hss != ''){
			hss = hss.substring(2);
			t = commonDictionarySplit(hss);
			if (t.url != ''){
				load_menu(t.app, t.url, t.cpp);
			}
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
		if ($('#submenu').css('top') == 'auto' && delta > 0){

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
			qurl = qurl.substring(2);
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

	//运营平台
	$.getJSON('<?php echo INDEX; ?>?m=develop&c=company&v=ajax_list&all=1', function(data){
		if (data.status == 0){
			companylist = data.list;
		}
	});
	//服务器
	$.getJSON('<?php echo INDEX; ?>?m=develop&c=server&v=ajax_list&all=1', function(data){
		if (data.status == 0){
			serverlist = data.list;
		}
	});
});
</script>
<script type="text/javascript" src="static/js/WdatePicker.js"></script>
</body>
</html>