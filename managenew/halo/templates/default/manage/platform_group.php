<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var pageIndex = 1, pageCount = 0, pageSize = 15, recordNum = 0, platformlist;
function getList(index){
	var query = "<?php echo INDEX; ?>?m=manage&c=priv&v=ajax_platform_list&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#platformlist" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			success: showList
		});
	});
}

function showList( data) {
	if (data.status == -1){
		$('#platformlist').html(data.msg);
	}else {
		recordNum = data.count;
		pageCount = Math.ceil( data.count/pageSize ), platformlist = data.list;
		$( ".pager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getList });
		$( "#platformlist" ).empty();
		if (data.count > 0){
			$( "#platformlisttpl" ).tmpl( platformlist ).prependTo( "#platformlist" );
			$( "#platformlist" ).stop(true,true).hide().slideDown(400);
		}
	}
}

var dialog;
$(document).ready(function(){
	if (typeof global_companylist != 'undefined') {
		$('#platformitemtpl').tmpl(global_companylist).appendTo('#platformitem');
	}

	getList(1);

	//checkbox所有
	$('#checkall').on('click', function(){
		if ($(this).is(':checked') == true){
			$(':checkbox', $('#platformitem')).each(function(){
				$(this).attr('checked', 'checked')
			});
		}else {
			$(':checkbox', $('#platformitem')).each(function(){
				$(this).removeAttr('checked')
			});
		}
	});
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
	// $('#extentfold').click();
	
	//添加 
	$('#post_submit').on('submit', function(e){
		e.preventDefault();
		$('#btnsubmit').attr('disabled', 'disabled');
		var objform = $(this);
		$.ajax({
				url: '<?php echo INDEX; ?>?m=manage&c=priv&v=platform_group',
				data: objform.serialize(),
				dataType: 'json',
				type: 'POST',
				success: function(data){
					var alertclassname = '', time = 2;
					switch (data.status){
						case 0: 
							alertclassname = 'alert_success'; 
							if (data.editflag == 1){
								getList( pageIndex );
								$('#gid').val(0);
								$('#btncancel').hide();
								$('#btnreset').show();
							}else {
								$( "#platformlisttpl" ).tmpl( data.info ).prependTo( "#platformlist" ).fadeIn(2000, function(){
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
					$(':checked', $('#platformitem')).each(function(){
						$(this).removeAttr('checked');
					});
					document.getElementById('post_submit').reset();
					setTimeout( function(){
						$('#op_tips').fadeOut();
						$('#btnsubmit').removeAttr('disabled');
					}, ( time * 1000 ) );
				}
			});
		return false;
	});
	//查看
	$('#platformlist').on('click', 'a.view', function(){
		var id = $(this).attr('data-id'), name = $(this).attr('data-name');
		if (id > 0){
			var cids = $(this).siblings('textarea').text();
			if (cids != ''){
				cids = trim(cids, ',');
				var arrcid = cids.split(',');
				var strlist = '';
				var i = 0;
				for(var key in global_companylist){
					for (i=0; i < arrcid.length; i++){
					    if (global_companylist[key].cid == arrcid[i]){
					    	strlist += global_companylist[key].name + '&nbsp;&nbsp;&nbsp;&nbsp;';
					    	break;
					    }
					}
				}
				if (strlist.length > 0){
					dialog = $.dialog({id: 'dialog_priv_'+id, title: name+'->组权限详情'});
					dialog.content(strlist);
				}
			}
		}
	});
	//修改
	$('#platformlist').on('click', 'a.edit', function(){
		if ($('#submit_area').is(':hidden')){
			$('#extentfold').click();
		}
		var id = $(this).attr('data-id');
		if (id > 0){
			for(var pkey in platformlist){
				if (platformlist[pkey].gid == id){
					$('#gid').val(platformlist[pkey].gid);
					$('#gname').val(platformlist[pkey].gname);
					$('#description').val(platformlist[pkey].description);
					if (platformlist[pkey].cids != ''){
						cids = trim(platformlist[pkey].cids, ',');
						cids = trim(cids, ',');
						var arrcid = cids.split(',');
						var strlist = '';
						var i = 0;
						$(':checked', $('#platformitem')).each(function(){
							$(this).removeAttr('checked');
						});
						for(var key in global_companylist){
							for (i=0; i < arrcid.length; i++){
							    if (global_companylist[key].cid == arrcid[i]){
							    	$(':checkbox[value="'+arrcid[i]+'"]', $('#platformitem')).attr('checked', 'checked');
							    	break;
							    }
							}
						}
					}
					
					$('#btncancel').show();
					$('#btnreset').hide();
					$('#gname').focus();
					$('#gname').css('border', '1px solid #E6791C');
					setTimeout( function(){	$('#gname').css('border', ''); }, ( 2000 ) );
					break;
				}
			}
		}
	});
	//--------取消修改
	$('#btncancel').on('click', function(){
		$('#gid').val('0');
		$(':checked', $('#platformitem')).each(function(){
			$(this).removeAttr('checked');
		});
		document.getElementById('post_submit').reset();
		
		$('#btncancel').hide();
		$('#btnreset').show();
	});
});
</script>

<script type="text/template" id="platformitemtpl">
	<input type="checkbox" name="cid[]" value="${cid}">${name}  
</script>

<script type="text/template" id="platformcheckedtpl">
	${name}&nbsp;&nbsp;&nbsp;&nbsp;
</script>

<script type="text/template" id="platformlisttpl">
<tr>
<td>${gid}</td>
<td>${gname}</td>
<td>${description}</td>
<td>${date('Y-m-d H:i', dateline)}</td>
<td>
<a href="javascript:;" data-id="${gid}" data-name="${gname}" class="view"><?php echo Lang('view') ?></a>
<a href="javascript:;" data-id="${gid}" class="edit"><?php echo Lang('edit') ?></a>
<a href="javascript:;" data-id="${gid}" class="delete"><?php echo Lang('delete') ?></a>
<textarea style="display:none">${cids}</textarea>
</td>
<td>&nbsp;</td>
</tr>
</script>

<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('platform_priv_title'); ?></span></a></li>
	</ul>
	<br class="clear">

	<!-- 添加用户 -->
	<div class="onecolumn">
		<div class="header">
			<h2><?php echo Lang('add_platform_priv_group'); ?></h2>
			<ul class="second_level_tab">
				<li><a href="javascript:;" id="extentfold"><?php echo Lang('show') ?></a></li>
			</ul>
		</div>
		<div class="content" id="submit_area" style="display: none;">
			<!-- Begin form elements -->
			<form name="post_submit" id="post_submit" action="<?php echo INDEX; ?>?m=manage&c=priv&v=platform_group" method="post">
				<div id="op_tips" style="display: none;"><p></p></div>
				<input type="hidden" name="gid" id="gid">
				<table class="global" width="100%" cellpadding="0" cellspacing="0">
					<tbody>
					<tr>
						<th style="width: 10%;"><?php echo Lang('group_name') ?></th>
						<td><input type="text" name="gname" id="gname" style="width:30%"></td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('allow_platform') ?></th>
						<td>
							<p><input type="checkbox" name="checkall" id="checkall" value="1"><?php echo Lang('all') ?></p>
							<p id="platformitem">
							</p>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('group_description') ?></th>
						<td><textarea type="text" name="description" id="description" style="width:32%;height:100px"></textarea></td>
					</tr>
					<tr>
						<td></td>
						<td> 
							<p>
							<input type="hidden" name="doSubmit" value="1">
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
			<!-- End form elements -->
		</div>
	</div>

	<br class="clear">
	
	<div class="onecolumn">
		<div class="header">
			<h2><?php echo Lang('platform_group_list') ?></h2>
			<ul class="second_level_tab"></ul>
		</div>
	</div>

	<div class="content">
		<div id="list_op_tips" style="display: none;"><p></p></div>
		<!-- Begin example table data -->
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<thead>
			    <tr>
			    	<th style="width:50px;">ID</th>
			    	<th style="width:15%"><?php echo Lang('group_name'); ?></th>
			    	<th style="width:20%"><?php echo Lang('group_description'); ?></th>
			    	<th style="width:120px;"><?php echo Lang('date'); ?></th>
			    	<th style="width:100px;"><?php echo Lang('operation') ?></th>
			    	<th>&nbsp;</th>
			    </tr>
			</thead>
			<tbody id="platformlist">
			   
			</tbody>
		</table>
		<div class="pagination pager" id="pager"></div>
		<!-- End pagination -->			
	</div>
</div>