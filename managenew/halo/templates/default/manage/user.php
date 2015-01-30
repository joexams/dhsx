<?php defined('IN_G') or exit('No permission resources.');
//include template('manage', 'header'); ?>
<script type="text/javascript">
var pageIndex = 1, pageCount = 0, pageSize = 15, recordNum = 0, userlist;
var jsonrole = <?php echo $data['jsonrole']; ?>;
function getUserList(index){
	var query = "<?php echo INDEX; ?>?m=manage&c=user&v=ajax_list&roleid=<?php echo $data['roleid']; ?>&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#userlist" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			success: showUserList
		});
	});
}
function getsearchList(index){
	var query = "<?php echo INDEX; ?>?m=manage&c=user&v=ajax_list&roleid=<?php echo $data['roleid']; ?>&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#userlist" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			data: $('#get_search_submit').serialize(),
			success: function(data){
				showUserList(data, 1);
			}
		});
	});
}

function get_rolename_byroleId(roleid){
	var rolename = '';
	for(var key in jsonrole){
		if (jsonrole[key].roleid == roleid){
			rolename = jsonrole[key].rolename;
			break;
		}
	}
	return rolename;
}

function showUserList( data, type) {
	if (data.status == -1){
		$('#userlist').html(data.msg);
	}else {
		recordNum = data.count;
		pageCount = Math.ceil( data.count/pageSize ), userlist = data.list;
		if (type != undefined && type == 1){
			$( ".pager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getsearchList });
		}else {
			$( ".pager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getUserList });
		}
		$( "#userlist" ).empty();
		if (data.count > 0){
			$( "#userlisttpl" ).tmpl( userlist ).prependTo( "#userlist" );
			$( "#userlist" ).stop(true,true).hide().slideDown(400);
			if (pageCount > 1){
				$( "#userlist" ).parent().parent('div.content').css('height', $('#userlist').parent('table.global').css('height'));
			}
		}
	}
}

var dialog;
$(document).ready(function(){
	getUserList( pageIndex );

	//--------添加
	$('#post_user_submit').on('submit', function(e){
		e.preventDefault();
		$('#btnsubmit').attr('disabled', 'disabled');
		var objform = $(this);
		$.ajax({
				url: '<?php echo INDEX; ?>?m=manage&c=user&v=add',
				data: objform.serialize(),
				dataType: 'json',
				type: 'POST',
				success: function(data){
					var alertclassname = '', time = 2;
					switch (data.status){
						case 0: 
							alertclassname = 'alert_success'; 
							if (data.editflag == 1){
								getUserList( pageIndex );
								$('#userid').val(0);
								$('#btncancel').hide();
								$('#btnreset').show();
							}else {
								$( "#userlisttpl" ).tmpl( data.info ).prependTo( "#userlist" ).fadeIn(2000, function(){
									var obj = $(this);
									obj.css('background', '#E6791C');
									setTimeout( function(){	obj.css('background', ''); }, ( 2000 ) );
								});	
							}
							break;
						case 1: alertclassname = 'alert_error'; break;
					}
					$('#op_tips').attr('class', alertclassname);
					$('#op_tips').children('p').html(data.msg);
					$('#op_tips').fadeIn();
					document.getElementById('post_user_submit').reset();
					setTimeout( function(){
						$('#op_tips').fadeOut();
						$('#btnsubmit').removeAttr('disabled');
					}, ( time * 1000 ) );
				},
                error: function() {
                    document.getElementById('post_user_submit').reset();
                }
			});
		return false;
	});
	//--------修改
	$('#userlist').on('click', 'a.edit', function(){
		if ($('#submit_area').is(':hidden')){
			$('#extentfold').click();
		}
		var obj    = $(this);
		var userid = obj.attr('data-userid');
		if (userid > 0){
			$.ajax({
				url: '<?php echo INDEX; ?>?m=manage&c=user&v=ajax_info',
				data: 'userid='+userid,
				dataType: 'json',
				type: 'get',
				success: function(data){
					var alertclassname = '', time = 2;
					switch (data.status){
						case 0:
							if (data.info.userid > 0){
								$('#userid').val(data.info.userid);
								$('#username').val(data.info.username);
								$('#roleid').val(data.info.roleid);
								$('#lang').val(data.info.lang);
								if (data.info.status == 1){
									$('#status1').attr('checked', 'checked');
								}else {
									$('#status0').attr('checked', 'checked');
								}
								$('#btncancel').show();
								$('#btnreset').hide();
								$('#username').focus();
								$('#username').css('border', '1px solid #E6791C');
								setTimeout( function(){	$('#username').css('border', ''); }, ( 2000 ) );
							}
							break;
						case 1: 
							alertclassname = 'alert_error'; 
							$('#op_tips').attr('class', alertclassname);
							$('#op_tips').children('p').html(data.msg);
							$('#op_tips').fadeIn();
							setTimeout( function(){$('#op_tips').fadeOut();}, ( time * 1000 ) );
							break;
					}
				}
			});
		}
	});

	//--------取消修改
	$('#btncancel').on('click', function(){
		$('#userid').val('0');
		document.getElementById('post_user_submit').reset();

		$('#btncancel').hide();
		$('#btnreset').show();
	});
	//---------权限
	$('#userlist').on('click', 'a.priv', function(){
		var userid = $(this).attr('data-userid'), roleid = $(this).attr('data-roleid'), username = $(this).attr('data-username');
		var title  = username + " <?php echo Lang('priv'); ?>";
		dialog = $.dialog({id: 'dialog_priv',title: '<?php echo Lang("loading"); ?>'});
		dialog.title(title);

		if (userid > 0){
			$.ajax({
				url: '<?php echo INDEX; ?>?m=manage&c=priv&v=ajax_show',
				data: 'userid='+userid+'&roleid='+roleid,
				success: function(data){
					dialog.content(data);
				}
			});
		}
		return false;
	});
	//---------平台权限
	$('#userlist').on('click', 'a.pf_priv', function(){
		var userid = $(this).attr('data-userid'), roleid = $(this).attr('data-roleid'), username = $(this).attr('data-username');
		var title  = username + " <?php echo Lang('platform').Lang('priv'); ?>";
		dialog = $.dialog({id: 'dialog_pf_priv', width: 500, title: '<?php echo Lang("loading"); ?>'});
		dialog.title(title);
		if (userid > 0){
			$.ajax({
				url: '<?php echo INDEX; ?>?m=manage&c=priv&v=ajax_platform_show',
				data: 'userid='+userid+'&roleid='+roleid,
				success: function(data){
					dialog.content(data);
				}
			});
		}
		return false;
	});
	//---------删除
	/*$('#userlist').on('click', 'a.delete', function(){
		var obj    = $(this);
		var userid = obj.attr('data-userid');
		var username = obj.attr('data-username');
		if (userid > 0){
			$.ajax({
				url: '<?php echo INDEX; ?>?m=manage&c=user&v=delete',
				data: 'userid='+userid+'&username='+username,
				dataType: 'json',
				type: 'post',
				success: function(data){
					var alertclassname = '', time = 2;
					switch (data.status){
						case 0: 
							alertclassname = 'alert_success'; 
							if (userid == $('#userid').val()){
								$('#userid').val('0');
								document.getElementById('post_user_submit').reset();
							}
							break;
						case 1: alertclassname = 'alert_error'; break;
					}
					$('#list_op_tips').attr('class', alertclassname);
					$('#list_op_tips').children('p').html(data.msg);
					$('#list_op_tips').fadeIn();
					obj.parent().parent('tr').remove();
					setTimeout( function(){
						$('#list_op_tips').fadeOut();
					}, ( time * 1000 ) );
				}
			});
		}
		return false;
	});*/
	
	//展开
	$('#extentfold').on('click', function(){
		var hidden = '<?php echo Lang("hidden"); ?>', show = '<?php echo Lang("show"); ?>';
		var obj = $(this);
		$('#submit_area').toggle("normal", function(){
			if ($(this).is(':hidden')){
				obj.html(show);
			}else {
				obj.html(hidden);
			}
		});
	});


	$('#get_search_submit').on('submit', function(e){
		e.preventDefault();
		recordNum = 0;
		getsearchList(1);
	});

	<?php if ($data['show']){ ?>
	$('#extentfold').click();
	<?php } ?>
});
</script>

<script type="text/template" id="userlisttpl">
<tr>
	<td>${userid}</td>
	<td>${username}</td>
	<td>${get_rolename_byroleId(roleid)}</td>
	<td>{{if status == 1}} <span class="redtitle">已冻结</span> {{else}} <span class="greentitle">正常</span> {{/if}}</td>
	<td>${lastloginip}&nbsp;</td>
	<td>${date('Y-m-d H:i:s', lastlogintime)}</td>
	<td>
		{{if roleid == 1}}
		<span class="graytitle"><?php echo Lang('priv_setting'); ?></span> | 
		<span class="graytitle"><?php echo Lang('platform').Lang('priv'); ?></span> | 
		<a href="javascript:;" class="edit" data-userid="${userid}"><?php echo Lang('edit') ?></a>
		{{else}}
		<a href="javascript:;" class="priv" data-roleid="${roleid}" data-userid="${userid}" data-username="${username}"><?php echo Lang('priv_setting'); ?></a> | 
		<a href="javascript:;" class="pf_priv" data-roleid="${roleid}" data-userid="${userid}" data-username="${username}"><?php echo Lang('platform').Lang('priv'); ?></a> | 
		<a href="javascript:;" class="edit" data-userid="${userid}"><?php echo Lang('edit') ?></a> 
		{{/if}}
	</td>
	<td>&nbsp;</td>
</tr>
</script>

<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('user_title'); ?></span></a></li>
	</ul>
	<br class="clear">

	<!-- 添加用户 -->
	<div class="onecolumn">
		<div class="header">
			<h2><?php echo Lang('add_user_title'); ?></h2>
			<ul class="second_level_tab">
				<li><a href="javascript:;" id="extentfold"><?php echo Lang('show') ?></a></li>
			</ul>
		</div>
		<div class="content" id="submit_area" style="display: none;">
			<!-- Begin form elements -->
			<form name="post_user_submit" id="post_user_submit" action="<?php echo INDEX; ?>?m=manage&c=user&v=add" method="post">
				<input type="hidden" name="doSubmit" value="1">
				<input type="hidden" name="userid" id="userid" value="0">
				<table class="global" width="100%" cellpadding="0" cellspacing="0">
					<tbody>
					<tr>
						<th style="width: 100px;"><?php echo Lang('username'); ?>：</th>
						<td style="width: 200px"><input type="text" name="username" id="username" style="width:90%"></td>
						<th style="width: 100px"><?php echo Lang('status'); ?></th>
						<td style="width: 200px">
							<input type="radio" name="status" id="status0" value="0">正常
							<input type="radio" name="status" id="status1" value="1">冻结
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('password'); ?>：</th>
						<td><input type="password" name="password" style="width:90%"></td>
						<th><?php echo Lang('re_password'); ?>：</th>
						<td><input type="password" name="repassword" style="width:90%"></td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('select_role'); ?>：</th>
						<td>
							<select name="roleid" id="roleid">
								<?php foreach ($data['rolelist'] as $key => $value) { ?>
									<option value="<?php echo $value['roleid']; ?>"><?php echo $value['rolename']; ?></option>
								<?php } ?>
					    	</select>
					    </td>
						<th><?php echo Lang('select_language'); ?>：</th>
						<td>
							<select name="lang" id="lang">
					      	    <option value="zh-cn">简体中文</option>
					      	    <option value="zh-tw">繁體中文</option>
					      	    <option value="en">English</option>
					    	</select>
					    </td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td></td>
						<td colspan="3"> 
							<p>
							<input type="submit" id="btnsubmit" class="button" value="<?php echo Lang('submit'); ?>">
							<input type="button" id="btncancel" class="button" style="display: none;" value="<?php echo Lang('edit_cancel'); ?>">
							<input type="reset" id="btnreset" class="button"  value="<?php echo Lang('reset'); ?>">
							</p>
						</td>
						<td>&nbsp;</td>
					</tr>
				</tbody>
				</table>
		    </form>
		    <div id="op_tips" style="display: none;"><p></p></div>
			<!-- End form elements -->
		</div>
	</div>

	<!-- 用户列表 -->
	<br class="clear">
	
	<div class="onecolumn">
		<div class="header">
			<h2><?php echo Lang('user_list') ?></h2>
			<ul class="second_level_tab"></ul>
		</div>
	</div>

	<div class="nav singlenav">
		<form id="get_search_submit" action="<?php echo INDEX; ?>?m=manage&c=user&v=ajax_list" method="get" name="form">
		<ul class="nav_li">
			<li class="nobg">
				<p>
					账号：	
					<input name="username" type="text" value="" size="20">	
					<input type="submit" name="getsubmit" id="get_search_submit" value="搜索" class="button_link">
					<input name="dogetSubmit" type="hidden" value="1">
				</p>
			</li>
		</ul>
		</form>
	</div>

	<div class="content">
		<div id="list_op_tips" style="display: none;"><p></p></div>
		<!-- Begin example table data -->
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<thead>
			    <tr>
			    	<!-- <th style="width:5%">
			    		<input type="checkbox" id="check_all" name="check_all">
			    	</th> -->
			    	<th style="width:50px;">ID</th>
			    	<th style="width:100px"><?php echo Lang('username'); ?></th>
			    	<th style="width:80px"><?php echo Lang('role'); ?></th>
					<th style="width:60px"><?php echo Lang('status'); ?></th>
			    	<th style="width:100px;"><?php echo Lang('lastloginip'); ?></th>
			    	<th style="width:120px;"><?php echo Lang('lastlogintime'); ?></th>
			    	<th style="width:200px;"><?php echo Lang('operation'); ?></th>
			    	<th>&nbsp;</th>
			    </tr>
			</thead>
			<tbody id="userlist">
			   
			</tbody>
		</table>
		<!-- End pagination -->			
	</div>
	<div class="pagination pager" id="pager"></div>
</div>
