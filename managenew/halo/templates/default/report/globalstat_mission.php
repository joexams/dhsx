<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var max;
$(function(){
	/**
	 * 运营平台
	 */
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

	$('#get_search_submit').on('submit', function(e){
		e.preventDefault();
		$('#getsubmit').attr('disabled', 'disabled');
		var objform = $(this), sid = $('#sid').val();
		if (sid > 0){
			$.ajax({
				url: '<?php echo INDEX; ?>?m=server&c=get&v=mission_stat',
				data: objform.serialize(),
				dataType: 'json',
				success: function(data){
					$('#missionlist').empty();
					if (data.status == 0 && data.list.length > 0){
						max = data.max;
						$('#missionlisttpl').tmpl(data.list).appendTo('#missionlist');
					}
					$('#getsubmit').removeAttr('disabled');
				},
				error: function(){
					$('#getsubmit').removeAttr('disabled');
				}
			});
		}else {
			$('#getsubmit').removeAttr('disabled');
		}
		return false;
	});
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
	<td>&nbsp;</td>
</tr>
</script>

<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('mission_process'); ?></span></a></li>
	</ul>
	<br class="clear">
	<div class="nav singlenav">
	    <form id="get_search_submit" action="<?php echo INDEX; ?>?m=report&c=get&v=mission_stat" method="get" name="form">
	    <ul class="nav_li">
	        <li>
				<p>
					<select name="cid" id="cid">
						<option value="0"><?php echo Lang('operation_platform') ?></option>
					</select>
					<select name="sid" id="sid">
						<option value="0"><?php echo Lang('all_server') ?></option>
					</select>
					<select name="type" id="type">
						<option value="0"><?php echo Lang('ord_replica'); ?></option>
						<option value="1"><?php echo Lang('hero_replica'); ?></option>
					</select>
				</p>
			</li>
			<li class="nobg">
				<p>
					<input type="submit" name="getsubmit" id="get_search_submit" value="搜索" class="button_link">
					<input type="hidden" name="dogetSubmit" value="1">
				</p>
			</li>
	    </ul>
	    </form>
	</div>

	<div class="content">
		<table class="global" width="100%" style="max-width:800px;min-width:600px" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
				    <th style="width:10%;"><?php echo Lang('mission') ?></th>
				    <th style="width:5%;"><?php echo Lang('arrivals') ?></th>
				    <th style="width:5%;"><?php echo Lang('completeds') ?></th>
				    <th style="width:5%;"><?php echo Lang('not_completeds') ?></th>
				    <th style="width:5%;"><?php echo Lang('pk_success_times') ?></th>
				    <th style="width:200px;">&nbsp;</th>
				    <th style="width:200px;">&nbsp;</th>
				    <th style="width:5%;"><?php echo Lang('pk_failed_times') ?></th>
				    <th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="missionlist">

			</tbody>
		</table>
	</div>
</div>