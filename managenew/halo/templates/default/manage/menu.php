<?php defined('IN_G') or exit('No permission resources.');
//include template('manage', 'header'); ?>
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

var zNodes = <?php echo $data['treelist']; ?>;

function beforeDrag(treeId, treeNode) {
	return false;
}

function beforeRemove(treeId, treeNode) {
	var msg = '';
	if (treeNode.children == undefined){
		msg = "<?php echo Lang('delete_menu_confirm') ?>";
	}else {
		msg = "<?php echo Lang('delete_menu_confirm_more') ?>";
	}
	msg = msg.replace(/\${menuname}/g, treeNode.name);
	return confirm(msg);
}

function beforeEditName(treeId, treeNode) {
	if ($('#submit_area').is(':hidden')) {
		$('#extentfold').click();
	}
	var menuid = treeNode.id;
	if (menuid > 0){
		$.ajax({
			url: '<?php echo INDEX; ?>?m=manage&c=menu&v=ajax_info',
			data: 'menuid='+menuid,
			dataType: 'json',
			type: 'get',
			success: function(data){
				var alertclassname = '', time = 2;
				switch (data.status){
					case 0:
						if (data.info.mid > 0){
							$('#mid').val(data.info.mid);
							$('#mname').val(data.info.mname);
							$('#modules').val(data.info.m);
							$('#views').val(data.info.v);
							$('#controls').val(data.info.c);
							$('#guidedata').val(data.info.data);
							$('#listorder').val(data.info.listorder);

							if (data.info.islink == 1){
								$('#islink1').attr('checked', 'checked');
								$('tr.islink').show();
							}else {
								$('#islink0').attr('checked', 'checked');
								$('tr.islink').hide();
							}
							$('#urllink').val(data.info.urllink);

							if (data.info.display == 1){
								$('#display1').attr('checked', 'checked');
							}else {
								$('#display0').attr('checked', 'checked');
							}
							if (data.info.isdistrib == 1){
								$('#isdistrib1').attr('checked', 'checked');
							}else {
								$('#isdistrib0').attr('checked', 'checked');
							}
							$('#parentid').val(data.info.parentid);

							// var obj = document.getElementById("mySelect");
							// var index = obj.selectedIndex;
							// obj.options[index] = new Option("three",3);  //更改对应的值
							// obj.options[index].selected = true;  //保持选中状态

							$('#btncancel').show();
							$('#btnreset').hide();
							$('#mname').focus();
							$('#mname').css('border', '1px solid #E6791C');
							setTimeout( function(){	$('#mname').css('border', ''); }, ( 2000 ) );
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
	var menuid = treeNode.id, menuname = treeNode.name;

	if (menuid > 0){
		$.ajax({
			url: '<?php echo INDEX; ?>?m=manage&c=menu&v=delete',
			data: 'menuid='+menuid+'&menuname='+menuname,
			dataType: 'json',
			type: 'post',
			success: function(data){
				var alertclassname = '', time = 2;
				switch (data.status){
					case 0: 
						alertclassname = 'alert_success'; 
						if (menuid == $('#mid').val()){
							$('#mid').val('0');
							document.getElementById('post_menu_submit').reset();
						}
						break;
					case 1: alertclassname = 'alert_error'; break;
				}
				$('#list_op_tips').attr('class', alertclassname);
				$('#list_op_tips').children('p').html(data.msg+'：'+menuname);
				$('#list_op_tips').fadeIn();
				setTimeout( function(){
					$('#list_op_tips').fadeOut();
				}, ( time * 1000 ) );
			}
		});
	}
}

$(document).ready(function(){
	//--------取消修改
	$('#btncancel').on('click', function(){
		$('#mid').val('0');
		document.getElementById('post_menu_submit').reset();

		$('#btncancel').hide();
		$('#btnreset').show();
	});
	//--------添加
	$('#post_menu_submit').on('submit', function(e){
		e.preventDefault();
		$('#btnsubmit').attr('disabled', 'disabled');
		var objform = $(this);
		$.ajax({
				url: '<?php echo INDEX; ?>?m=manage&c=menu&v=add',
				data: objform.serialize(),
				dataType: 'json',
				type: 'POST',
				success: function(data){
					var alertclassname = '', time = 2;
					switch (data.status){
						case 0: 
							alertclassname = 'alert_success'; 
							if (data.editflag == 1){
								$('#mid').val(0);
								$('#btncancel').hide();
								$('#btnreset').show();
							}else {
								zNodes.push(data.info);
								$.fn.zTree.init($("#menutree"), setting, zNodes);
							}
							break;
						case 1: alertclassname = 'alert_error'; break;
					}
					$('#op_tips').attr('class', alertclassname);
					$('#op_tips').children('p').html(data.msg);
					$('#op_tips').fadeIn();
					document.getElementById('post_menu_submit').reset();
					setTimeout( function(){
						$('#op_tips').fadeOut();
						$('#btnsubmit').removeAttr('disabled');
					}, ( time * 1000 ) );
				}
			});
	});

	$.fn.zTree.init($("#menutree"), setting, zNodes);

	$('a', $('#menutree')).on({
		mouseover: function(){
			$(this).addClass('ruled');
		},
		mouseout: function(){
			$(this).removeClass('ruled');
		}
	});
	/**
	 * 展开与收缩
	 * @return {[type]} [description]
	 */
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

	$('#submit_area').on('click', '.radiolink', function(){
		var islink = $(this).val();
		if (islink == 1){
			$('tr.islink').show();
		}else {
			$('tr.islink').hide();
			$('#urllink').val('');
		}
	});
});
</script>

<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('menu_title'); ?></span></a></li>
	</ul>
	<br class="clear">

	<!-- 添加用户 -->
	<div class="onecolumn">
		<div class="header">
			<h2><?php echo Lang('add_menu_title'); ?></h2>
			<ul class="second_level_tab">
				<li><a href="javascript:;" id="extentfold"><?php echo Lang('show') ?></a></li>
			</ul>
		</div>
		<div class="content" id="submit_area" style="display:none">
			<!-- Begin form elements -->
			<form name="post_menu_submit" id="post_menu_submit" action="<?php echo INDEX; ?>?m=manage&c=menu&v=add" method="post">
				<input type="hidden" name="doSubmit" value="1">
				<input type="hidden" name="mid" id="mid" value="0">
				<table class="global" width="100%" cellpadding="0" cellspacing="0">
					<tr>
						<th style="width: 10%;"><?php echo Lang('menu_name'); ?>：</th>
						<td style="width: 20%"><input type="text" name="mname" id="mname" style="width:90%"></td>
						<th style="width: 10%"><?php echo Lang('parent_menu_name'); ?>：</th>
						<td style="width: 20%">
							<select name="parentid" id="parentid">
								<option value="0"><?php echo Lang('default_parent'); ?></option>
								<?php foreach ($data['menulist'] as $key => $value) { ?>
								<option value="<?php echo $value['id'] ?>"><?php echo $value['name'] ?></option>
								<?php } ?>
							</select>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('is_link') ?>：</th>
						<td>
							<input type="radio" name="islink" class="radiolink" id="islink0" value="0" checked ><?php echo Lang('no'); ?>
							<input type="radio" name="islink" class="radiolink" id="islink1" value="1" ><?php echo Lang('yes'); ?>
						</td>
						<th><?php echo Lang('is_distrib'); ?></th>
						<td>
							<input type="radio" name="isdistrib" value="0" id="isdistrib0"><?php echo Lang('can'); ?>
							<input type="radio" name="isdistrib" value="1" id="isdistrib1"><?php echo Lang('canot'); ?>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr class="islink" style="display:none;">
						<th><?php echo Lang('link_url') ?>：</th>
						<td><input type="text" name="urllink" id="urllink" style="width:50%"></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr class="nolink">
						<th><?php echo Lang('modules'); ?>：</th>
						<td><input type="text" name="m" id="modules" style="width:90%"></td>
						<th><?php echo Lang('controls'); ?>：</th>
						<td><input type="text" name="c" id="controls" style="width:90%"></td>
						<td>&nbsp;</td>
					</tr>
					<tr class="nolink">
						<th><?php echo Lang('views'); ?>：</th>
						<td><input type="text" name="v" id="views" style="width:90%"></td>
						<th><?php echo Lang('guidedata'); ?>：</th>
						<td><input type="text" name="data" id="guidedata" style="width:90%"></td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('displaystatus'); ?>：</th>
						<td>
							<input type="radio" name="display" id="display0" value="0" checked><?php echo Lang('displaynone'); ?>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<input type="radio" name="display" id="display1" value="1"><?php echo Lang('display'); ?>
						</td>
						<th><?php echo Lang('listsort'); ?>：</th>
						<td>
							<input type="text" name="listorder" id="listorder" value="0" style="width:90%">
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
			<h2><?php echo Lang('menu_list') ?></h2>
			<ul class="second_level_tab"></ul>
		</div>	
	</div>
	<div class="content">
	<div id="list_op_tips" style="display: none;"><p></p></div>
	</div>
	<div class="content ztree" id="menutree">
		<!-- Begin example table data -->

		<!-- End pagination -->
	</div>	
</div>