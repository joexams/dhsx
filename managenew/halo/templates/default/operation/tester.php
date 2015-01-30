<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var pageIndex = 1, pageCount = 0, pageSize = 15, recordNum = 0, cid = -1, testerlist;
function getList(index){
	var query = "<?php echo INDEX; ?>?m=develop&c=server&v=server_list&type=list&cid="+cid+"&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#testerlist" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			success: showList
		});
	});
}

function showList( data ) {
	if (data.status == 1){
		$('#testerlist').html(data.msg);
	}else {
		if (cid == 0 && data.count == 0){
			$('option[value="0"]', $('#companyul')).remove();
			$('#companyul').change();
			return false;
		}
		recordNum = data.count;
		pageCount = Math.ceil( data.count/pageSize ), testerlist = data.list, cid = data.cid;
		$( ".pager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getList });
		$( "#testerlist" ).empty();
		if (data.count > 0){
			$( "#testerlisttpl" ).tmpl( testerlist ).prependTo( "#testerlist" );
			$( "#testerlist" ).stop(true,true).hide().slideDown(400);

			var arrsid = [], arrcid = [];
			for(var key in testerlist){
				arrsid[key] = testerlist[key].sid;
				arrcid[key] = testerlist[key].cid;
			}

			$.ajax({
				url: '<?php echo INDEX; ?>?m=operation&c=interactive&v=ajax_tester_list',
				data: {sid: arrsid.join(','), cid: arrcid.join(',')},
				dataType: 'json',
				success: function(datas){
					if (datas.status == 0){
						if (datas.list.length > 0){
							for (var key in datas.list){
								$('#userlisttpl').tmpl(datas.list[key].testers).appendTo($('#t_'+datas.list[key].cid+'_'+datas.list[key].sid).find('td.tester'));
							}
						}
					}
				}
			});
		}
	}
}
$(document).ready(function(){
	getList(1);
	/**
	 * 刷新测试号
	 * @return {[type]} [description]
	 */
	$("#testerlist").on('click', 'a.refresh', function(){
		var obj = $(this), sid = obj.attr('sid'), lcid = obj.attr('cid');
		if (sid > 0){
			$.ajax({
				url: '<?php echo INDEX; ?>?m=operation&c=interactive&v=tester_refresh',
				data: {cid: lcid, sid: sid},
				dataType: 'json',
				success: function(data){
					if (data.status == 0){
						obj.parent().siblings('td.tester').empty();
						$('#userlisttpl').tmpl(data.list).appendTo(obj.parent().siblings('td.tester'));
					}
				}
			});
		}
	});
	/**
	 * 移除单个测试号
	 * @return {[type]} [description]
	 */
	$('#testerlist').on('click', 'a.remove', function(){
		var obj = $(this), id = obj.attr('data-id'), username = obj.attr('data-name'), nickname = obj.attr('data-nick');
		var cid = obj.parent().parent().find('input[name^="cid"]').val();
		var sid = obj.parent().parent().find('input[name^="sid"]').val();

		if (id > 0 && cid>0 && sid>0 && confirm('你确定删除测试号【'+nickname+'】吗？')){
			$.ajax({
				url: '<?php echo INDEX; ?>?m=operation&c=source&v=give',
				data: {id: id, key: 'set_tester', player_type: 1, tid: 5, cid: cid, sid: sid, doSubmit: 1, player: username},
				type: 'POST',
				dataType: 'json',
				success: function(data){
					if (data.status == 0){
						obj.remove();
					}
				}
			})
		}
		return false;
	});
	/**
	 * 移除单个服务所有测试号
	 * @return {[type]} [description]
	 */
	$('#testerlist').on('click', 'a.clear', function(){
		var obj = $(this), servername = obj.attr('data-name');
		var cid = obj.parent().parent().find('input[name^="cid"]').val();
		var sid = obj.parent().parent().find('input[name^="sid"]').val();

		if (cid>0 && sid>0 && confirm('你确定清除服务器【'+servername+'】下所有测试号吗？')){
			$.ajax({
				url: '<?php echo INDEX; ?>?m=operation&c=source&v=give_more',
				data: {cid: cid, sid: sid, op_type: 2},
				type: 'POST',
				dataType: 'json',
				success: function(data){
					var alertclassname = '', time = 2;
					switch (data.status){
						case 0: 
							alertclassname = 'alert_success'; 
							$('#t_'+cid+'_'+sid).empty();
							break;
						case 1: alertclassname = 'alert_error'; break;
					}
					$('#list_op_tips').attr('class', alertclassname);
					$('#list_op_tips').children('p').html(data.msg);
					$('#list_op_tips').fadeIn();
					setTimeout( function(){
						$('#list_op_tips').fadeOut();
					}, ( time * 1000 ) );
				}
			});
		}
		return false;
	});

	/**
	 * 选择所有
	 * @return {[type]} [description]
	 */
	$('#checkall').on('change', function(){
		if ($(this).is(':checked')){
			$('input:checkbox', $('#testerlist')).each(function(){
				$(this).attr('checked', 'checked');
			});
		}else {
			$('input:checkbox', $('#testerlist')).each(function(){
				$(this).removeAttr('checked');
			});
		}
	});

	$('#post_submit').on('submit', function(e){
		e.preventDefault();
		$('#btnsubmit').attr('disabled', 'disabled');
		var objform = $(this), op_type = $('#op_type').val();
		if (confirm('您确定执行此批量操作？')){
			$.ajax({
					url: '<?php echo INDEX; ?>?m=operation&c=source&v=give_more',
					data: objform.serialize(),
					dataType: 'json',
					type: 'POST',
					success: function(data){
						var alertclassname = '', time = 2;
						switch (data.status){
							case 0: 
								alertclassname = 'alert_success'; 
								if (op_type == 2){
									$(':checked[name^="sid"]').each(function(){
										$(this).parent().parent().find('td.tester').empty();
									});
								}
								break;
							case 1: alertclassname = 'alert_error'; break;
						}
						$('#op_tips').attr('class', alertclassname);
						$('#op_tips').children('p').html(data.msg);
						$('#op_tips').fadeIn();
						document.getElementById('post_submit').reset();
						setTimeout( function(){
							$('#op_tips').fadeOut();
							$('#btnsubmit').removeAttr('disabled');
						}, ( time * 1000 ) );
					}
				});
		}else {
			$('#btnsubmit').removeAttr('disabled');
		}
		return false;
	});
});
</script>
<script type="text/template" id="userlisttpl">
	<a href="javascript:;" data-id="${id}" data-name="${username}" data-nick="${nickname}" class="remove">${nickname}</a>&nbsp;&nbsp;
</script>
<script type="text/template" id="testerlisttpl">
<tr id="t_${cid}_${sid}">
	<td>
	<input type="checkbox" name="sid[]" value="${sid}">
	<input type="hidden" name="cid[]" value="${cid}">
	</td>
	<td>${name}-${o_name}</td>
	<td class="tester">&nbsp;</td>
	<td>
		<a href="javascript:;" class="clear" data-name="${name}"><?php echo Lang('clear').Lang('tester') ?></a>
		<a href="javascript:;" class="refresh" cid="${cid}" sid="${sid}"><?php echo Lang('refresh').Lang('tester') ?></a>
	</td>
	<td>&nbsp;</td>
</tr>
</script>

<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('tester') ?></span></a></li>
	</ul>
	<br class="clear">

	<form name="post_submit" id="post_submit" action="<?php echo INDEX; ?>?m=operation&c=source&v=give_more" method="post">
	<div class="content">
		<!-- Begin form elements -->
		<div id="list_op_tips" style="display: none;"><p></p></div>
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th style="width:30px;"><input type="checkbox" name="checkall" id="checkall" value="1"></th>
					<th style="width:10%"><?php echo Lang('server'); ?></th>
					<th style="width:40%"><?php echo Lang('tester'); ?></th>
					<th style="width:150px;"><?php echo Lang('operation'); ?></th>
					<th>&nbsp;</th>
				</tr>
			</thead>

			<tbody id="testerlist">

			</tbody>
		</table>
		<div class="pagination pager" id="pager"></div>
		<!-- End form elements -->
	</div>

	<div class="content" id="submit_area">
		<!-- Begin form elements -->
			<div id="op_tips" style="display: none;"><p></p></div>
			<table class="global" width="100%" cellpadding="0" cellspacing="0">
				<tr class="betop">
					<th style="width: 10%;"><?php echo Lang('op_type') ?></th>
					<td style="width: 30%;"> 
						<select name="op_type" id="op_type">
							<option value="1"><?php echo Lang('give') ?></option>
							<option value="2"><?php echo Lang('clear').Lang('tester') ?></option>
						</select>
					</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<th><?php echo Lang('give').Lang('ingot') ?></th>
					<td> 
						<input type="text" name="ingot" id="ingot">
					</td>
					<td>&nbsp;</td>
				</tr>
				<tbody id="sourcetmpl">

				</tbody>
				<tr>
					<th><?php echo Lang('give').Lang('coins') ?></th>
					<td> 
						<input type="text" name="coins" id="coins" >
					</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<th><?php echo Lang('change_vip_level') ?></th>
					<td>
						<select name="vip_level" id="vip_level">
							 <option value="-1"><?php echo Lang('does_not_operation') ?></option>
							 <option value="0"><?php echo Lang('cancel_vip') ?></option>
							 <option value="1">VIP1</option>
							 <option value="2">VIP2</option>
							 <option value="3">VIP3</option>
							 <option value="4">VIP4</option>
							 <option value="5">VIP5</option>
							 <option value="6">VIP6</option>
							 <option value="7">VIP7</option>
							 <option value="8">VIP8</option>
							 <option value="9">VIP9</option>
							 <option value="10">VIP10</option>
							 <option value="11">VIP11</option>
							 <option value="12">VIP12</option>
						</select>
					</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td></td>
					<td> 
						<p>
						<input type="submit" id="btnsubmit" class="button" value="<?php echo Lang('submit'); ?>">
						<input type="reset" id="btnreset" class="button"  value="<?php echo Lang('reset'); ?>">
						</p>
					</td>
					<td>&nbsp;</td>
				</tr>
			</table>
		<!-- End form elements -->
	</div>
	</form>

</div>
