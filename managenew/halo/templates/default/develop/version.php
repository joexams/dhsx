<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var pageIndex = 1, pageCount = 0, pageSize = 50, recordNum = 0, versionlist, type = 0;
function getList(index, type){
	var query = "<?php echo INDEX; ?>?m=develop&c=version&v=ajax_setting_list&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#versionlist" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			success: showList
		});
	});
}
function showList( data ) {
	if (data.status == 1){
		$('#versionlist').html(data.msg);
	}else {
		recordNum = data.count;
		pageCount = Math.ceil( data.count/pageSize ), versionlist = data.list, type = data.type;
		$( ".pager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getList });
		$( "#versionlist" ).empty();
		if (data.count > 0){
			$( "#versionlisttpl" ).tmpl( versionlist ).prependTo( "#versionlist" );
			$( "#versionlist" ).stop(true,true).hide().slideDown(400);
		}
	}
}
$(document).ready(function() {
	getList(1);
	/**
	 * 提交
	 * @param  {[type]} e [description]
	 * @return {[type]}   [description]
	 */
	$('#post_submit').on('submit', function(e){
		e.preventDefault();
		$('#btnsubmit').attr('disabled', 'disabled');
		var objform = $(this);
		$.ajax({
				url: '<?php echo INDEX; ?>?m=develop&c=version&v=setting',
				data: objform.serialize(),
				dataType: 'json',
				type: 'POST',
				success: function(data){
					var alertclassname = '', time = 2;
					switch (data.status){
						case 0: 
							alertclassname = 'alert_success'; 
							$( "#versionlisttpl" ).tmpl( data.info ).prependTo( "#versionlist" ).fadeIn(2000, function(){
								var obj = $(this);
								obj.css('background', '#E6791C');
								setTimeout( function(){	obj.css('background', ''); }, ( 2000 ) );
							});	
							break;
						case 1: alertclassname = 'alert_error'; break;
					}
					$('#list_op_tips').attr('class', alertclassname);
					$('#list_op_tips').children('p').html(data.msg);
					$('#list_op_tips').fadeIn();
					document.getElementById('post_submit').reset();
					setTimeout( function(){
						$('#list_op_tips').fadeOut();
						$('#btnsubmit').removeAttr('disabled');
					}, ( time * 1000 ) );
				}
			});
		return false;
	});
	/**
	 * 点击修改
	 * @return {[type]} [description]
	 */
	$('#versionlist').on('click', 'span.entryline', function(){
		var eleid = $(this).parent().parent('tr').attr('id');
		var id = eleid.replace(/entry-/, '');
		if (id > 0){
			for(var key in versionlist){
				if (versionlist[key].id == id){
					$('#'+eleid).html( $('#editversiontpl').tmpl(versionlist[key]) );
					break;
				}
			}
		}
	});
	/**
	 * 保存修改 
	 * @return {[type]} [description]
	 */
	$('#versionlist').on('click', 'input.btnsave', function(){
		var objtr = $(this).parent().parent('tr')
		var eleid = objtr.attr('id');
		var id = eleid.replace(/entry-/, '');
		if (id > 0){
			var dateline = objtr.find('input[name="dateline"]').val();
			var version = objtr.find('input[name="version"]').val();
			var content = objtr.find('textarea[name="content"]').val();
			$.ajax({
				url: '<?php echo INDEX; ?>?m=develop&c=version&v=setting',
				data: {id: id, version: version, content: content, dateline: dateline, doSubmit: 1},
				dataType: 'json',
				type: 'POST',
				success: function(data){
					if (data.status == 0){
						for(var key in versionlist){
							if (versionlist[key].id == id){
								versionlist[key].version = version;
								versionlist[key].content = content;
								versionlist[key].dateline = data.info.dateline;
								$('#'+eleid).html( $('#versionlisttpl').tmpl(versionlist[key]).html() );
								break;
							}
						}
					}
				}
			});
		}
	});
	/**
	 * 取消修改
	 * @return {[type]} [description]
	 */
	$('#versionlist').on('click', 'input.btncancel', function(){
		var eleid = $(this).parent().parent('tr').attr('id');
		var id = eleid.replace(/entry-/, '');
		if (id > 0){
			for(var key in versionlist){
				if (versionlist[key].id == id){
					$('#'+eleid).html( $('#versionlisttpl').tmpl(versionlist[key]).html() );
					break;
				}
			}
		}
	});
	/**
	 * 删除
	 * 
	 * @return {[type]} [description]
	 */
	$('#versionlist').on('click', 'a.delete', function(){
		var obj    = $(this);
		var id = obj.attr('data-id');
		if (id > 0){
			$.ajax({
				url: '<?php echo INDEX; ?>?m=develop&c=version&v=ajax_setting_delete',
				data: 'id='+id,
				dataType: 'json',
				type: 'post',
				success: function(data){
					var alertclassname = '', time = 2;
					switch (data.status){
						case 0: alertclassname = 'alert_success'; break;
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
	});
});
</script>

<script type="text/template" id="editversiontpl">
	<td>${id}</td>
	<td><input type="text" name="dateline" value="${date('Y-m-d', dateline)}"></td>
	<td><input type="text" name="version" value="${version}"></td>
	<td>
		<textarea name="content" cols="40" rows="2">${content}</textarea>
	</td>
	<td>
	<input type="button" class="btnsave btn" value="<?php echo Lang('save'); ?>">
	<input type="button" class="btncancel" value="<?php echo Lang('cancel'); ?>">
	<td>&nbsp;</td>
	</td>
</script>

<script type="text/template" id="versionlisttpl">
	<tr id="entry-${id}">
		<td>${id}</td>
		<td><span class="entryline">${date('Y-m-d', dateline)}</span></td>
		<td><span class="entryline">${version}</span></td>
		<td><span class="entryline">${content}</span></td>
		<td>
			<a href="javascript:;" data-id="${id}" class="delete"><?php echo Lang('delete') ?></a>
		</td>
		<td>&nbsp;</td>
	</tr>
</script>

<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('version_update_log') ?></span></a></li>
	</ul>
	<br class="clear">
	<div class="content" id="submit_area">
		<!-- Begin form elements -->
		<form name="post_submit" id="post_submit" action="<?php echo INDEX; ?>?m=develop&c=version&v=setting">
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th style="width:80px;">ID</th>
					<th style="width:80px;"><?php echo Lang('date'); ?></th>
					<th style="width:80px;"><?php echo Lang('version'); ?></th>
					<th style="width:30%"><?php echo Lang('update_content'); ?></th>
					<th style="width:50px;"><?php echo Lang('operation') ?></th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<tr class="select_ruled">
					<th><?php echo Lang('add_new_record') ?></th>
					<td><input type="text" name="dateline" id="dateline" readonly onclick="WdatePicker()" value="<?php echo $data['today'] ?>" size="10"></td>
					<td><input type="text" name="version" id="version"></td>
					<td>
						<textarea name="content" id="content" cols="40" rows="2"></textarea>
					</td>
					<td>
						<input type="hidden" name="doSubmit" value="1">
						<input type="submit" id="btnsubmit" class="button" value="提交"></td>
					<td>&nbsp;</td>
				</tr>
			</tbody>
			<tbody id="versionlist">

			</tbody>
		</table>
		</form>
		<div id="list_op_tips" style="display: none;"><p></p></div>
		<div class="pagination pager" id="pager"></div>		
		<!-- End form elements -->
	</div>
</div>
