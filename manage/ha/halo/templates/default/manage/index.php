<?php defined('IN_G') or exit('No permission resources.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--[if IE 7]><meta http-equiv="X-UA-Compatible" content="IE=7" /><![endif]-->
<!--[if lte IE 6]><meta http-equiv="X-UA-Compatible" content="IE=6" /><![endif]-->
<title><?php echo Lang('title'); ?></title>
<link type="text/css" rel="stylesheet" href="static/css/view.css" />
<link type="text/css" rel="stylesheet" href="static/css/default.css" />
<link type="text/css" rel="stylesheet" href="static/css/jquery.treetable.css" />
</head>
<body class="body_bg">
<div class="notification-bar ajax-notification-bar"><a href="javascript:;" class="close">&times;</a></div>
<div id="header">
	<div class="logo">
		<a href="/" title="大话神仙"><img src="static/images/logo.png"  alt="大话神仙" /></a>
	</div>
	<div class="nav_cont">
		<div class="menunav">
			<ul id="mainmenu"></ul>
		</div>
	</div>
	<div class="login">
		<a class="change_link"><?php echo param::get_cookie('username')?></a>
		<a href="#app=6&url=<?php echo urlencode(WEB_URL.INDEX.'?m=manage&c=user&v=edit_password') ?>" class="c_txt change_link" rel="<?php echo INDEX; ?>?m=manage&c=user&v=edit_password"><?php echo Lang('edit_password') ?></a>
		<a href="<?php echo INDEX; ?>?m=manage&c=index&v=logout" class="change_link"><?php echo Lang('logout') ?></a>
	</div>
</div>

<div id="page_title" class="main_webstie cf" style="padding-top:30px;">
    <div class="title" id="site_info"></div>
    <div class="optmod" id="setting_wrapper"></div>
</div>

<div class="dashboard cf" id="wrap_common">
	<div id="sidebar" class="sidebar" style="display:none">
		<div class="subnav" id="submenu">
			<h3 id="menu_title"></h3>
		</div>
	</div>
	<div id="main" class="main">
		<div id="module"></div>
		<div id="footer">
			<p>
				Copyright &copy; 2013 Halo. All Rights Reserved.
			</p>
		</div>
	</div>
</div>

<div>
	<div>
	<div id="js_N_nav_footer_trigger" class="sitemap-ctrl sitemap-ctrl-active" style="position: fixed;">
		快捷导航<i class="i_orderd"></i>
	</div>
    <div id="js_N_nav_footer" class="fixed-sitemap js_N_nav_footer_nav_fixed" style="bottom: 0px;">
	    <div class="fixed-sitemap-main">
	        <div class="sitemap-flink" id="quickguidelist">
	            <a href="javascript:;" id="add_quick" title="添加快捷导航" style="margin-left:30px;"><span><i class="i_add">+</i>添加</span></a>
	        </div>
	    </div>
    </div>
    </div>
</div>

<script type="text/template" id="mainmenutpl">
{{if mid == 1}}
	<li class="current"><a href="./<?php echo INDEX; ?>">${mname}</a></li>
{{else islink == 1}}
	<li><a href="${urllink}" target="_blank">${mname}</a></li>
{{else}}
	<li><a href="#app=${mid}" data-mid="${mid}" rel="<?php echo INDEX; ?>?m=${m}&c=${c}&v=${v}${data}" class="a_txt">${mname}</a></li>
{{/if}}
</script>

<script type="text/template" id="submenutpl">
{{if typeof(thirdmenu) != 'undefined' && thirdmenu != false}}

	{{if islink == 1}}
    <h3 class="lBlue">${mname}</h3>
    {{else}}
    <h3 class="lBlue" data-mid="${mid}">${mname}</h3>
    {{/if}}

    <ul id="slide_${mid}">
    	{{each thirdmenu}}
    		{{if islink == 1}}
    		<li><a href="${urllink}" target="_blank"><span>${mname}</span></a></li>
    		{{else}}
    		<li><a href="#app={{if parentid==122 || parentid==121}}120{{else}}${parentpath.substring(1,2)}{{/if}}&cpp=${mid}&url=${encodeurl(m, v, c, data)}" rel="<?php echo INDEX; ?>?m=${m}&c=${c}&v=${v}${data}" data-mid="${mid}" class="b_txt"><span>${mname}</span></a></li>
    		{{/if}}
		{{/each}}
    </ul>
{{else}}

        {{if islink == 1}}
        <h3 class="lBlue">${mname}</h3>
        {{else}}
        <h3 class="lBlue b_txt" rel="<?php echo INDEX; ?>?m=${m}&c=${c}&v=${v}${data}" data-mid="${mid}">${mname}</h3>
        {{/if}}
        
{{/if}}
</script>

<script type="text/template" id="quickguidetpl">
<a href="${qurl}" class="qview" id="qview_${qid}">${qname}</a><a href="javascript:;" class="qdelete" data-id="${qid}" id="qdelete_${qid}"><i class="i_close"></i></a>
</script>

<script type="text/template" id="global_companylisttpl">
<option value="${cid}">${fn} - ${name}</option>
</script>

<script type="text/template" id="global_serverlisttpl">
<option value="${sid}" data-ver="${server_ver}">${name}-${o_name}</option>
</script>

<!-- 头部 -->
<script type="text/javascript" src="static/js/jquery.min.js"></script>
<script type="text/javascript" src="static/js/highcharts.js"></script>
<script type="text/javascript" src="static/js/jquery.tmpl.min.js"></script>
<script type="text/javascript" src="static/js/jquery.pager.js"></script>
<script type="text/javascript" src="static/js/jquery.artDialog.min.js"></script>
<script type="text/javascript" src="static/js/artDialog.plugins.min.js"></script>
<script type="text/javascript" src="static/js/common.js"></script>
<script type="text/javascript" src="static/js/jquery.history.js"></script>
<script type="text/javascript" src="static/js/ha.js"></script>
<script type="text/javascript" src="static/js/hachart.js"></script>

<script type="text/javascript">
var mod = $('#module');
var weburl = '<?php echo WEB_URL ?>', 
	global_index = '<?php echo INDEX; ?>',
	default_menu = '<?php echo $data['default'];?>';
var global_companylist = [], global_serverlist = [];
var menujson = <?php echo $data['menu']['main'] ? $data['menu']['main'] : '{}'; ?>, 
    submenujson = <?php echo $data['menu']['sub'] ? $data['menu']['sub'] : '{}'; ?>;
var quickguide = <?php echo $data['quicklist'] ? $data['quicklist'] : '[]' ?>;

$(function(){
	$( "#mainmenutpl" ).tmpl( menujson ).prependTo( "#mainmenu" );
	$.history.init(pageload);

	//默认加载
	var r=/(?:Firefox|GranParadiso|Iceweasel|Minefield).(\d+\.\d+)/i;
	var isfirefox = parseFloat((r.exec(navigator.userAgent)||r.exec('Firefox/3.3'))[1],10);
	var t, hs= isfirefox ? ((t=location.href.split("#"))&&t[1]?("#"+t[1]):''):location.hash;
	if((t=hs.indexOf("#")) == -1){
		Ha.common.loadImg('module');
		load_menu(<?php echo $data['default'];?>);
	}

	$('#submenu').on('click', 'a.b_txt', function(){
		var loadurl = $(this).attr('rel'), loadmid = $(this).attr('data-mid');
		$('.current', $('#submenu')).removeClass('current');
		$(this).parent().addClass('current');
	});

	$('tbody[id$="list"] tr').live({
		mouseover: function(){
			$(this).addClass('hover');
		},
		mouseout: function(){
			$(this).removeClass('hover');
		}
	});

	/**
	 * 添加快捷方式
	 * @return {[type]} [description]
	 */
	$('#quickguidelist').on('click', 'a#add_quick', function(){
		var url = '<?php echo INDEX; ?>?m=manage&c=quickguide&v=public_setting';
		var queryData = 'qurl='+encodeURIComponent(window.location)+'&qname='+$('#tt').text();
		Ha.common.ajax(url, 'html', queryData, 'get', 'container', function(data){
			Ha.Dialog.show(data, '添加快捷方式', 500, 'quickguideDlg');
		}, 1);
	});
	
	$('#js_N_nav_footer_trigger').css({
	    "left": ($(window).width()-980)/2 + 980 - $('#js_N_nav_footer_trigger').width(),
	    "top": $(window).height() - 77
	});

	$('#js_N_nav_footer_trigger').on('click', function(){
		$('#js_N_nav_footer').toggle(100, function(){
			if ($(this).is(':visible') === false) {
				$('#js_N_nav_footer_trigger i').attr('class', 'i_orderu');
				$('#js_N_nav_footer_trigger').css('top', $(window).height()-32);
			}else {
				$('#js_N_nav_footer_trigger i').attr('class', 'i_orderd');
				$('#js_N_nav_footer_trigger').css('top', $(window).height()-77);
			}
		});
	});
	$('#quickguidetpl').tmpl(quickguide).prependTo('#quickguidelist');

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
			var url = '<?php echo INDEX; ?>?m=manage&c=quickguide&v=public_delete';
			Ha.common.ajax(url, 'json', {qid: qid}, 'get', 'container', function(data){
				if (data.status == 0){
					$('#qview_'+qid).remove();
					$('#qdelete_'+qid).remove();
				}
			}, 1);
		}
	});
});

global_companylist = <?php echo $companylist ? $companylist : '[]'; ?>;
global_serverlist = <?php echo $serverlist ? $serverlist : '[]'; ?>;
</script>
<script type="text/javascript" src="static/js/WdatePicker.js"></script>
</body>
</html>
