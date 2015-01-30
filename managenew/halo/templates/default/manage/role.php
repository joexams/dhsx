<?php defined('IN_G') or exit('No permission resources.');
//include template('manage', 'header'); ?>
<script type="text/javascript">
var pageIndex = 1, pageCount = 0, pageSize = 10, recordNum = 0, rolelist;
function getRoleList(index){
	var query = "<?php echo INDEX; ?>?m=manage&c=role&v=ajax_list&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#rolelist" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			success: showRoleList
		});
	});
}
function showRoleList( data ) {
	if (data.status == -1){
		$('#rolelist').html(data.msg);
	}else {
		if (data.count > 0){
			recordNum = data.count;
			pageCount = Math.ceil( data.count/pageSize ), rolelist = data.list;
			$( "#pager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getRoleList });
			$( "#rolelist" ).empty();
			$( "#rolelisttpl" ).tmpl( rolelist ).prependTo( "#rolelist" );
			$( "#rolelist" ).stop(true,true).hide().slideDown(400);
		}
	}
}

var dialog = dialog != undefined ? null : '';
$(document).ready(function(){
	getRoleList( pageIndex );
	//--------添加
	$('#post_role_submit').on('submit', function(e){
		e.preventDefault();
		$('#btnsubmit').attr('disabled', 'disabled');
		var objform = $(this);
		$.ajax({
				url: '<?php echo INDEX; ?>?m=manage&c=role&v=add',
				data: objform.serialize(),
				dataType: 'json',
				type: 'POST',
				success: function(data){
					var alertclassname = '', time = 2;
					switch (data.status){
						case 0: 
							alertclassname = 'alert_success'; 
							if (data.editflag == 1){
								getRoleList( pageIndex );
								$('#roleid').val(0);
								$('#btncancel').hide();
								$('#btnreset').show();
							}else {
								$( "#rolelisttpl" ).tmpl( data.info ).prependTo( "#rolelist" ).fadeIn(2000, function(){
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
					document.getElementById('post_role_submit').reset();
					setTimeout( function(){
						$('#op_tips').fadeOut();
						$('#btnsubmit').removeAttr('disabled');
					}, ( time * 1000 ) );
				}
			});
	});
	//--------修改
	$('#rolelist').on('click', 'a.edit', function(){
		if ($('#submit_area').is(':hidden')){
			$('#extentfold').click();
		}
		var obj    = $(this);
		var roleid = obj.attr('data-roleid');
		if (roleid > 0){
			$.ajax({
				url: '<?php echo INDEX; ?>?m=manage&c=role&v=ajax_info',
				data: 'roleid='+roleid,
				dataType: 'json',
				type: 'get',
				success: function(data){
					var alertclassname = '', time = 2;
					switch (data.status){
						case 0:
							if (data.info.roleid > 0){
								$('#roleid').val(data.info.roleid);
								$('#rolename').val(data.info.rolename);
								$('#description').val(data.info.description);
								$('#listorder').val(data.info.listorder);
								if (data.info.disabled == 1){
									$('#disabled1').attr('checked', 'checked');
								}else {
									$('#disabled0').attr('checked', 'checked');
								}

								$('#btncancel').show();
								$('#btnreset').hide();
								$('#rolename').focus();
								$('#rolename').css('border', '1px solid #E6791C');
								setTimeout( function(){	$('#rolename').css('border', ''); }, ( 2000 ) );
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
		$('#roleid').val('0');
		document.getElementById('post_role_submit').reset();

		$('#btncancel').hide();
		$('#btnreset').show();
	});
	//-----------权限
	$('#rolelist').on('click', 'a.priv', function(){
		var roleid = $(this).attr('data-roleid'), rolename = $(this).attr('data-rolename');
		var title  = rolename+' <?php echo Lang('priv_setting') ?>';
		dialog = $.dialog({id: 'dialog_priv',title: title});
		dialog.title(title);

		$.ajax({
		    url: '<?php echo INDEX; ?>?m=manage&c=priv&v=ajax_show',
		    data: 'roleid='+roleid+'&rolename='+rolename,
		    success: function (data) {
		        dialog.content(data);
		    },
		    cache: false
		});
	});

	//---------删除
	$('#rolelist').on('click', 'a.delete', function(){
		var obj    = $(this);
		var roleid = obj.attr('data-roleid');
		var rolename = obj.attr('data-rolename');
		if (roleid > 0){
			$.ajax({
				url: '<?php echo INDEX; ?>?m=manage&c=role&v=delete',
				data: 'roleid='+roleid+'&rolename='+rolename,
				dataType: 'json',
				type: 'post',
				success: function(data){
					var alertclassname = '', time = 2;
					switch (data.status){
						case 0: 
							alertclassname = 'alert_success'; 
							if (roleid == $('#roleid').val()){
								$('#roleid').val('0');
								document.getElementById('post_role_submit').reset();
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
	});

	$('#rolelist').on('click', 'a.member', function() {
		var obj    = $(this);
		var roleid = obj.attr('data-roleid');
		var rolename = obj.attr('data-rolename');
		if (roleid > 0){
			location.hash = 'app=2&cpp=10&url='+encodeurl('manage', 'init', 'user', '&roleid='+roleid);
		}
	});

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
});
</script>

<script type="text/template" id="rolelisttpl">
<tr>
	<td>${roleid}</td>
	<td>${rolename}</td>
	<td>${description}</td>
	<td>
	{{if disabled == 0}}<span class="greentitle"><?php echo Lang('abled'); ?>{{else}}<<span class="redtitle"></span><?php echo Lang('disabled'); ?>{{/if}}</span>
	</td>
	<td>
		{{if roleid == 1}} 
		<span class="graytitle"><?php echo Lang('priv_setting'); ?></span> | 
		<a href="javascript:;" class="member" data-roleid="${roleid}" data-rolename="${rolename}"><?php echo Lang('member_manage'); ?></a> | 
		<span class="graytitle"><?php echo Lang('edit') ?></span>
		{{else}}
		<a href="javascript:;" class="priv" data-roleid="${roleid}" data-rolename="${rolename}"><?php echo Lang('priv_setting'); ?></a> | 
		<a href="javascript:;" class="member" data-roleid="${roleid}" data-rolename="${rolename}"><?php echo Lang('member_manage'); ?></a> | 
		<a href="javascript:;" class="edit" data-roleid="${roleid}"><?php echo Lang('edit') ?></a>
		{{/if}}
	</td>
	<td>&nbsp;</td>
</tr>
</script>


<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('role_title'); ?></span></a></li>
	</ul>
	<br class="clear">


	<!-- 添加用户 -->
	<div class="onecolumn">
		<div class="header">
			<h2><?php echo Lang('add_role_title'); ?></h2>
			<ul class="second_level_tab">
				<li><a href="javascript:;" id="extentfold"><?php echo Lang('show') ?></a></li>
			</ul>
		</div>
		<div class="content" id="submit_area" style="display:none;">
			<!-- Begin form elements -->
			<form name="post_role_submit" id="post_role_submit" action="<?php echo INDEX; ?>?m=manage&c=role&v=add" method="post">
				<input type="hidden" name="doSubmit" value="1">
				<input type="hidden" name="roleid" id="roleid" value="0">
				<table class="global" width="100%" cellpadding="0" cellspacing="0">
					<tr>
						<th style="width: 10%;"><?php echo Lang('rolename'); ?>：</th>
						<td style="width:50%"><input type="text" name="rolename" id="rolename" style="width:50%"></td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th style="vertical-align: top;"><?php echo Lang('description'); ?>：</th>
						<td><textarea rows="5" name="description" id="description" cols="35" style="width:40%"></textarea></td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('disabledstatus'); ?>：</th>
						<td>
							<input type="radio" name="disabled" id="disabled0" value="0" checked><?php echo Lang('abled'); ?>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<input type="radio" name="disabled" id="disabled1" value="1"><?php echo Lang('disabled'); ?>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('listsort'); ?>：</th>
						<td>
							<input type="text" name="listorder" id="listorder" value="0">
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td></td>
						<td> 
							<p>
							<input type="submit" id="btnsubmit" class="button" value="<?php echo Lang('submit'); ?>">
							<input type="button" id="btncancel" class="button" style="display: none;" value="<?php echo Lang('edit_cancel'); ?>">
							<input type="reset" id="btnreset" class="button"  value="<?php echo Lang('reset'); ?>">
							</p>
						</td>
						<td>&nbsp;</td>
					</tr>
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
			<h2><?php echo Lang('role_list') ?></h2>
			<ul class="second_level_tab">
			</ul>
		</div>
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
			    	<th style="width:60px;"><?php echo Lang('rolename'); ?></th>
			    	<th style="width:20%"><?php echo Lang('description'); ?></th>
			    	<th style="width:50px;"><?php echo Lang('disabledstatus'); ?></th>
			    	<th style="width:200px;"><?php echo Lang('operation'); ?></th>
			    	<th>&nbsp;</th>
			    </tr>
			</thead>
			<tbody id="rolelist">
			   
			</tbody>
		</table>
		<div class="pagination" id="pager">
		</div>
		<!-- End pagination -->		
	</div>		
</div>
