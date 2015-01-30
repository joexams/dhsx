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
<link type="text/css" rel="stylesheet" href="static/css/jquery.treetable.theme.default.css" />

<script language = "javaScript" src = "static/js/common.js" type="text/javascript"></script>
<script language = "javaScript" src = "static/js/jquery.min.js" type="text/javascript"></script>
<script language = "javaScript" src = "static/js/ha.js" type="text/javascript"></script>
<script language = "javaScript" src = "static/js/jquery.artDialog.min.js" type="text/javascript"></script>
<script language = "javaScript" src = "static/js/artDialog.plugins.min.js" type="text/javascript"></script>
<script language = "javaScript" src = "static/js/jquery.treetable.js" type="text/javascript"></script>

</head>
<body>
<div class="notification-bar ajax-notification-bar"><a href="javascript:;" class="close">&times;</a></div>
<div id="header">
	<div class="logo">
		<a href="/" title="大话神仙"><img src="static/images/logo.png"  alt="大话神仙" /></a>
	</div>
</div>

<div id="page_title" class="main_webstie cf" style="padding-top:30px; backgroup:#fff">

    <div class="optmod" id="setting_wrapper"></div>
</div>

<div class="dashboard cf" id="wrap_common" style="max-width:1140px;">
	<div id="main" class="main">
		<div id="module" class="column">
		<div id="container"></div>
<table id="menu_table">
<thead>
  <tr>
    <th colspan="9"><a href="<?php echo WEB_URL.INDEX?>">菜单管理</a></th>
  </tr>

  <tr>
    <th>菜单名称</th>   
    <th>方法文件</th>
    <th>表</th>
    <th>参数</th>
    <th>链接url</th>
    <th>外链</th>
    <th>父目录</th>
    <th>启用</th>
    <th>编辑</th>
    </tr>
    </thead>
    <tbody>
    <?php echo $tree;?>
    <tr>
     <td><input id="describe" name="describe" type="text" value="" onclick="select();" size="15"/></td>

     <td><input id="func_file" name="func_file" type="text" value="" onclick="select();"size="15"/></td>
     <td><input id="table_name" name="table_name" type="text" value="" onclick="select();"size="15"/></td>
     <td><input id="params" name="params" type="text" value="" onclick="select();"size="15"/></td>
     <td><input id="url" name="url" type="text" value="" onclick="select();" size="25"/></td>
     <td>
	 <select id="is_link" name="is_link">
	 	<option value="0">否</option><option value="1">是</option>
	 </select>
	 </td>
     <td>
     <select id="father_id_n" name="father_id_n">
		<?php echo $select_tree;?>
     </select>
     </td>
	 <td>
	 <select id="status" name="status">
	 	<option value="1">启用</option><option value="0">禁用</option>
	 </select>
	 </td>
	 <td>&nbsp</td>
  </tr>
  <tr>
    <td colspan="9" align="center">
  <input type="submit" id="Submit" name="Submit" value="执行操作" onClick='add()'  class="btn_sbm"/>
  </td>
  </tr>  </tbody>
</table>
		</div>
		<div id="footer">
			<p>
				Copyright &copy; 2013 Halo. All Rights Reserved.
			</p>
		</div>
	</div>
</div>
<script type="text/javascript">
$("#menu_table").treetable({ expandable: true });
var wh_url = "<?php echo WEB_URL.INDEX;?>";

function add (){
	var describe = $("#describe").val();
	var func_file = $("#func_file").val();
	var table_name = $("#table_name").val();
	var params = escape($("#params").val());
	var add_url = escape($("#url").val());
	var father_id_n = $("#father_id_n").val();
	var status = $("#status").val();
	var is_link = $("#is_link").val();
	var aurl = wh_url+"?m=manage&c=index&v=add";
	var queryAddData = 'describe='+describe+'&func_file='+func_file+'&table_name='+table_name+'&params='+params+'&father_id='+father_id_n+'&status='+status+'&url='+add_url+'&is_link='+is_link;
	if ($.trim(describe)){
		if ($.trim(table_name)){
			var url = wh_url+"?m=manage&c=index&v=check_table";
			var queryData = 'table_name='+table_name;
			Ha.common.ajax(url,'',queryData,'','',function afa(da){
				if (da.status == 0){
					Ha.notify.show(da.msg, 'normal', 'error');
					$("#table_name").focus();
					return false;
				}else{
					Ha.common.ajax(aurl,'',queryAddData,'','',function ref(){
						location.reload();
					});
				}
			},'1');
		}else{
			Ha.common.ajax(aurl,'',queryAddData,'','',function ref(){
				location.reload();
			});
		}
	}
}

function del (id,table_name){
	if (confirm("确认要删除？")){
		var aurl = wh_url+"?m=manage&c=index&v=delete";
		var queryData = 'id='+id+'&table_name='+table_name;
		Ha.common.ajax(aurl,'',queryData,'','',function ref(){
			location.reload();
		});
	}
}
$('.m_modify').live('click', function(e){
		e.preventDefault();
		var mid = $(this).parent().parent().attr('data-tt-id');
		var desc = $(this).parent().parent().children('td').eq(0).html();
		var table_name = $(this).parent().parent().children('td').eq(2).html();
		if (table_name != ''){
			table_name = '('+table_name+')';
		}
		var url = wh_url+'?m=manage&c=index&v=menu_modify';
		Ha.common.ajax(url, 'html', 'mid='+mid, 'get', 'container', function(data){
			Ha.Dialog.show(data, desc+table_name, 600, 'shake-demo');
		}, 1);
		return false;
	});
var modify_id='';
function modify (self){
	var tid = self.parentNode.parentNode.id;
	modify_id = modify_id+tid+'|';
	$("#modify_id").val(modify_id);
}

function show_add (fid,desc){
	var baseText;
	baseText = '<div class="column"><table><thead><tr><th>菜单名称</th><th>方法文件</th><th>表</th><th>参数</th><th>链接url</th><th>是否外链</th><th>父目录</th><th>是否启用</th></tr></thead><tbody><tr><td><input type="text" id="ndesc" value=""></td><td><input type="text" id="nfunc_file" value=""></td><td><input type="text" id="ntable_name" value=""></td><td><input type="text" id="nparams" value=""></td><td><input type="text" id="nurl" value=""></td><td><select id="nis_link"><option value="0">否</option><option value="1">是</option></select></td><td><select id="nfather_id">';
	$.ajax({
		dataType: 'json',
		url: wh_url+"?m=manage&c=index&v=edit",
		type: 'get',
		data: 'fid='+fid,
		success: function(data){
			baseText += data;
			baseText += '</select></td><td><select id="nstatus"><option value="1">启用</option><option value="0">禁用</option></select></td></tr></tbody></table><br>';
				$.dialog({
					id: 'shake-demo',
					title: desc,
					content: baseText+'</div>',
					ok: function () {
						submitAdd();
					},
					okValue: '提交',
					cancel: function () {}
				});
		}
	});
}
function submitAdd (){
	var describe = $("#ndesc").val();
	var func_file = $("#nfunc_file").val();
	var table_name = $("#ntable_name").val();
	var params = escape($("#nparams").val());
	var add_url = escape($("#nurl").val());
	var father_id_n = $("#nfather_id").val();
	var status = $("#nstatus").val();
	var is_link = $("#nis_link").val();
	var aurl = wh_url+"?m=manage&c=index&v=add";
	var queryAddData = 'describe='+describe+'&func_file='+func_file+'&table_name='+table_name+'&params='+params+'&father_id='+father_id_n+'&status='+status+'&url='+add_url+'&is_link='+is_link;
	if ($.trim(describe)){
		if ($.trim(table_name)){
			var url = wh_url+"?m=manage&c=index&v=check_table";
			var queryData = 'table_name='+table_name;
			Ha.common.ajax(url,'',queryData,'','',function afa(da){
				if (da.status == 0){
					Ha.notify.show(da.msg, 'normal', 'error');
					$("#ntable_name").focus();
					return false;
				}else{
					Ha.common.ajax(aurl,'',queryAddData,'','',function ref(){
						location.reload();
					});
				}
			},'1');
		}else{
			Ha.common.ajax(aurl,'',queryAddData,'','',function ref(){
				location.reload();
			});
		}
	}
}

function upd_furl(id){
	var aurl = wh_url+"?m=manage&c=index&v=upd_furl";
	var queryAddData = 'id='+id;
	Ha.common.ajax(aurl,'',queryAddData,'','',function ref(){
//		location.reload();
	});
}

</script>
</body>
</html>
