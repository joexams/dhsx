<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var max;
$(function(){
	/**
	 * 运营平台
	 */
	<?php if (!$cid && !$sid) { ?>
	if (typeof global_companylist != 'undefined') {
		$('#global_companylisttpl').tmpl(global_companylist).appendTo('#cid');
	}

	$('#cid').on('change', function(){
		var cid = $(this).val();
		if (cid > 0 && typeof global_serverlist != 'undefined'){
			$('option[value!="0"]', $('#sid')).remove();
			$('#global_serverlisttpl').tmpl(getServerByCid(cid)).appendTo('#sid');
		}else {
			$('option[value!="0"]', $('#sid')).remove();
		}
	});
	<?php } ?>

	$('#get_search_submit').on('submit', function(e){
		e.preventDefault();
		var objform = $(this), sid = $('#sid').val();
		if (sid > 0){
			var url = '<?php echo INDEX; ?>?m=report&c=globalstat&v=mission';
			Ha.common.ajax(url, 'json',  objform.serialize(), 'get', 'container', function(data){
				if (data.status == 0 && data.list.length > 0){
					max = data.max;
					$('#missionlist').empty().append($('#missionlisttpl').tmpl(data.list)).show();
				}else {
					$('#missionlist').html('<tr><td colspan="6" style="text-align: left">没有找到数据。</td></tr>');
				}
			}, 1);
		}else {
			Ha.notify.show('请先选择查询', '', 'error');
		}
		return false;
	});
	<?php if ($cid > 0 && $sid > 0) { ?>
	$('#get_search_submit').submit();
	<?php } ?>
});
</script>

<script type="text/template" id="missionlisttpl">
<tr id="mission_${mission_id}">
	<td>${town}-${mission}</td>
	<td>${parseInt(finished)+parseInt(notfinished)}</td>
	<td>${finished}</td>
	<td>${notfinished}</td>
	<td><strong style="color:#058DC7">${pktimes}</strong></td>
	<td style="padding-right:0;">&nbsp;<p style="float: right;background-color:#058DC7;width:${ pktimes > 0 ? Math.round(pktimes/max * 180) + 5 : 0}px">&nbsp;</p></td>
	<td style="padding-left:0;">&nbsp;<p style="float: left;background-color:#ED561B;width:${pkfailedtimes > 0 ? Math.round(pkfailedtimes/max * 180) + 5 : 0}px">&nbsp;</p></td>
	<td><strong style="color:#ED561B">${pkfailedtimes}</strong></td>
</tr>
</script>

<h2><span id="tt"><?php echo Lang('mission_process'); ?></span></h2>
<div class="container" id="container">
	<div class="toolbar">
		<div class="tool_date">
			<div class="title cf">
				<form id="get_search_submit" method="get" name="form">
				<div class="tool_group">

					<?php if ($cid > 0 && $sid > 0) { ?>
					<input type="hidden" name="cid" id="cid"  value="<?php echo $cid ?>">
					<input type="hidden" name="sid" id="sid"  value="<?php echo $sid ?>">
					<?php }else { ?>
					 <select name="cid" id="cid" class="ipt_select">
						<option value="0"><?php echo Lang('operation_platform') ?></option>
					</select>
					<select name="sid" id="sid" class="ipt_select">
						<option value="0"><?php echo Lang('all_server') ?></option>
					</select>
					<?php } ?>
					<select name="type" id="type" class="ipt_select">
						<option value="0"><?php echo Lang('ord_replica'); ?></option>
						<option value="1"><?php echo Lang('hero_replica'); ?></option>
					</select>
					 <input name="dogetSubmit" type="hidden" value="1">
					 <input type="submit" class="btn_sbm" value="查 询">
				</div>
				</form>
			</div>
		</div>
	</div>
	<div class="column cf" id="table_column">
		<div id="dataTable">
		<table>
		<thead>
		<tr id="dataTheadTr">
	    	<th><?php echo Lang('mission') ?></th>
		    <th><?php echo Lang('arrivals') ?></th>
		    <th><?php echo Lang('completeds') ?></th>
		    <th><?php echo Lang('not_completeds') ?></th>
		    <th><?php echo Lang('pk_success_times') ?></th>
		    <th>&nbsp;</th>
		    <th>&nbsp;</th>
		    <th><?php echo Lang('pk_failed_times') ?></th>
		</tr>
		</thead>
		<tbody id="missionlist">
		<tr><td colspan="6" style="text-align: left">请先选择需要查询的游戏服。</td></tr>
		</tbody>
		</table>
		</div>
		<div id="pageJs" class="page">
	        <div id="pager" class="page">
	        </div>
		</div>
	</div>
</div>