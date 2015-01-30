<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript" src="static/js/jquery.ztree.core.min.js"></script>
<script type="text/javascript" src="static/js/jquery.ztree.exedit.min.js"></script>
<script type="text/javascript">
//菜单树
var setting = {
	edit: {
		enable: true
	},
	data: {
		simpleData: {
			enable: true
		}
	},
	callback: {
		beforeDrag: beforeDrag,
		beforeEditName: beforeEditName,
		beforeRemove: beforeRemove,
		onRemove: noderemove
	}
};
var zNodes = <?php echo $data['treelist'] ?>;
function beforeDrag(treeId, treeNode){
	return false;
}

function beforeRemove(treeId, treeNode) {
	var msg = '';
	if (treeNode.children == undefined){
		msg = "<?php echo Lang('delete_block_confirm') ?>";
	}else {
		msg = "<?php echo Lang('delete_block_confirm_more') ?>";
	}
	msg.replace(/${blockname}/, treeNode.name);
	return confirm(msg);
}
function beforeEditName(treeId, treeNode){
	if ($('#submit_area').is(':hidden')){
		$('#extentfold').click();
	}
	var blockid = treeNode.id;
	if (blockid > 0){
		$.ajax({
			url: '<?php echo INDEX; ?>?m=manage&c=block&v=ajax_info',
			data: 'blockid='+blockid,
			dataType: 'json',
			type: 'get',
			success: function(data){
				var alertclassname = '', time = 2;
				switch (data.status){
					case 0:
						if (data.info.bid > 0){
							$('#bid').val(data.info.bid);
							$('#bname').val(data.info.bname);
							$('#listorder').val(data.info.listorder);
							$('#parentid').val(data.info.parentid);
							$('#version').val(data.info.version);
							$('#key').val(data.info.key);
							$('#btncancel').show();
							$('#btnreset').hide();
							$('#bname').focus();
							$('#bname').css('border', '1px solid #E6791C');
							setTimeout( function(){	$('#bname').css('border', ''); }, ( 2000 ) );
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
	return false;
}

function noderemove(e, treeId, treeNode) {
	var blockid = treeNode.id, blockname = treeNode.name;

	if (blockid > 0){
		$.ajax({
			url: '<?php echo INDEX; ?>?m=manage&c=block&v=delete',
			data: 'blockid='+blockid+'&blockname='+blockname,
			dataType: 'json',
			type: 'post',
			success: function(data){
				var alertclassname = '', time = 2;
				switch (data.status){
					case 0: 
						alertclassname = 'alert_success'; 
						if (blockid == $('#bid').val()){
							$('#bid').val('0');
							document.getElementById('post_block_submit').reset();
						}
						break;
					case 1: alertclassname = 'alert_error'; break;
				}
				$('#list_op_tips').attr('class', alertclassname);
				$('#list_op_tips').children('p').html(data.msg+'：'+blockname);
				$('#list_op_tips').fadeIn();
				setTimeout( function() {
					$('#list_op_tips').fadeOut();
				}, ( time * 1000 ) );
			}
		});
	}
}

$(document).ready(function(){
	//--------添加
	$('#post_block_submit').on('submit', function(e){
		e.preventDefault();
		$('#btnsubmit').attr('disabled', 'disabled');
		var objform = $(this);
		$.ajax({
				url: '<?php echo INDEX; ?>?m=manage&c=block&v=add',
				data: objform.serialize(),
				dataType: 'json',
				type: 'POST',
				success: function(data){
					var alertclassname = '', time = 2;
					switch (data.status){
						case 0: 
							alertclassname = 'alert_success'; 
							if (data.editflag == 1){
								$('#bid').val(0);
								$('#btncancel').hide();
								$('#btnreset').show();
							}else {
								zNodes.push(data.info);
								$.fn.zTree.init($("#blocktree"), setting, zNodes);
							}
							break;
						case 1: alertclassname = 'alert_error'; break;
					}
					$('#op_tips').attr('class', alertclassname);
					$('#op_tips').children('p').html(data.msg);
					$('#op_tips').fadeIn();
					document.getElementById('post_block_submit').reset();
					setTimeout( function(){
						$('#op_tips').fadeOut();
						$('#btnsubmit').removeAttr('disabled');
					}, ( time * 1000 ) );
				}
			});
	});

	//--------取消修改
	$('#btncancel').on('click', function(){
		$('#bid').val('0');
		document.getElementById('post_block_submit').reset();

		$('#btncancel').hide();
		$('#btnreset').show();
	});

	$.fn.zTree.init($("#blocktree"), setting, zNodes);

	$('a', $('#blocktree')).live({
		mouseover: function(){
			$(this).addClass('ruled');
		},
		mouseout: function(){
			$(this).removeClass('ruled');
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


<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('block_title'); ?></span></a></li>
	</ul>
	<br class="clear">

	<!-- 添加用户 -->
	<div class="onecolumn">
		<div class="header">
			<h2><?php echo Lang('add_block_title'); ?></h2>
			<ul class="second_level_tab">
				<li><a href="javascript:;" id="extentfold"><?php echo Lang('show') ?></a></li>
			</ul>
		</div>
		<div class="content" id="submit_area" style="display:none">
			<!-- Begin form elements -->
			<form name="post_block_submit" id="post_block_submit" action="<?php echo INDEX; ?>?m=manage&c=block&v=add" method="post">
				<input type="hidden" name="doSubmit" value="1">
				<input type="hidden" name="bid" id="bid" value="0">
				<table class="global" width="100%" cellpadding="0" cellspacing="0">
					<tr>
						<th style="width: 10%;"><?php echo Lang('block_name'); ?>：</th>
						<td style="width: 20%;"><input type="text" name="bname" id="bname" style="width:90%"></td>
						<th style="width: 10%;"><?php echo Lang('parent_block_name'); ?>：</th>
						<td style="width: 20%;">
							<select name="parentid" id="parentid">
								<option value="0"><?php echo Lang('default_parent'); ?></option>
								<?php foreach ($data['blocklist'] as $key => $value) { ?>
								<option value="<?php echo $value['id'] ?>"><?php echo $value['name'] ?></option>
								<?php } ?>
							</select>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('block_key'); ?>：</th>
						<td>
							<input type="text" name="key" id="key" value="" style="width:90%">
						</td>
						<th><?php echo Lang('version'); ?>：</th>
						<td>
							<select name="version" id="version">
								<option value=""><?php echo Lang('no_setting') ?></option>
								<?php echo $str_version; ?>
							</select>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('listsort'); ?>：</th>
						<td>
							<input type="text" name="listorder" id="listorder" value="" style="width:90%">
						</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td colspan="3"> 
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
			<h2><?php echo Lang('block_list') ?></h2>
		</div>	
	</div>
	<div class="content">
	<div id="list_op_tips" style="display: none;"><p></p></div>
	</div>
	<div class="content ztree" id="blocktree" style="border: 1px solid #666;">
		<!-- Begin example table data -->

		<!-- End pagination -->
		<br class="clear">				
	</div>		
</div>