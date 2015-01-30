<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
Ha.page.recordNum = 0;
Ha.page.pageCount = 0;
Ha.page.pageSize = 15;
Ha.page.listEid = 'customerlist';
Ha.page.colspan = 7;
Ha.page.emptyMsg = '<?php echo Lang('not_customer_click_to_add_customer')?>';
Ha.page.url = "<?php echo INDEX; ?>?m=manage&c=user&v=ajax_list&roleid=5";

var jsonrole = <?php echo $data['jsonrole']; ?>;
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

$(function() {
	Ha.page.getList(1);	

	$('#customerlist').on('click', 'a.priv', function() {
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

	//--------修改
	$('#customerlist').on('click', 'a.edit', function(){
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
								$( "#customerlisttpl" ).tmpl( data.info ).prependTo( "#customerlist" ).fadeIn(2000, function(){
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
				}
			});
		return false;
	});
});
</script>

<script type="text/template" id="customerlisttpl">
<tr>
	<td>${username}  <span class="graytitle">(${get_rolename_byroleId(roleid)})</span></td>
	<td>${typeof lastlogintime != 'undefined' ? date('Y-m-d H:i', lastlogintime):'&nbsp;'}</td>
	<td>${typeof lastloginip!='undefined' ? lastloginip : '&nbsp;'}</td>
	<td>${typeof logintimes!='undefined' ? logintimes : 0}</td>
	<td>${status > 0 ? '<?php echo Lang('lock'); ?>': '<?php echo Lang('normal'); ?>'}</td>
	<td><a href="javascript:;" data-userid="${userid}" data-roleid="${roleid}" data-username="${username}" class="priv"><?php echo Lang('priv'); ?></a>  <a href="javascript:;" class="edit" data-userid="${userid}" ><?php echo Lang('edit'); ?></a></td>
</tr>
</script>



<h2><span id="tt"><?php echo Lang('customer_manage'); ?></span></h2>
<div class="container" id="container">
	<div class="column cf" id="table_column">
		<div class="title">
	        <div class="more" id="tblMore">
	            <div id="div_pop">
	                <input class="btn_thin1" type="button" value="<?php echo Lang('add_user_title'); ?>">
	            </div>
	        </div>
	        <?php echo Lang('user_list') ?>
	    </div>
		<div id="dataTable">
		<table>
			<thead>
			    <tr>
			    	<th><?php echo Lang('login_account'); ?></th>
			    	<th><?php echo Lang('lastlogintime'); ?></th>
			    	<th><?php echo Lang('lastloginip'); ?></th>
			    	<th><?php echo Lang('login_count'); ?></th>
			    	<th><?php echo Lang('status'); ?></th>
			    	<th><?php echo Lang('operation'); ?></th>
			    </tr>
			</thead>
			<tbody id="customerlist">
			   
			</tbody>
		</table>
	</div>
	<div id="pageJs" class="page">
        <div id="pager" class="page">
        </div>
	</div>
	</div>
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
							<input type="radio" name="status" id="status0" value="0"><?php echo Lang('normal')?>
							<input type="radio" name="status" id="status1" value="1"><?php echo Lang('freeze')?>
						</td>
						<td>&nbsp;</td>
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
								<option value="0"><?php echo Lang('select_user_role'); ?></option>
								<option value="5"><?php echo Lang('customer_services')?></option>
					    	</select>
					    </td>
						<th><?php echo Lang('select_language'); ?>：</th>
						<td>
							<select name="lang" id="lang">
					      	    <option value="zh-cn"><?php echo Lang('default_select_language')?></option>
					      	    <option value="zh-tw"><?php echo Lang('traditional_chinese')?></option>
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