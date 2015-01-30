<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function(){
	$('#global_companylisttpl').tmpl(global_companylist).appendTo('.cid');

	$('.cid').on('change', function() {
		var cid = $(this).val();
		if (cid > 0){
			$('option[value!="0"]', $('.sid')).remove();
			$('#global_serverlisttpl').tmpl(getServerByCid(cid)).appendTo('.sid');
		}else {
			$('option[value!="0"]', $('.sid')).remove();
		}
	});

	$('#post_submit').on('submit', function(e){
		e.preventDefault();
		var objform = $(this), cid = $('#cid').val(), sid = $('#sid').val(), player = $.trim($('#player').val());
		var backid =  $('input:checked', $('#backlist')).val();
		var backtime = $('input:checked', $('#backlist')).attr('data-back-time');
		if (cid > 0 && sid > 0 && player != '' && parseInt(backid) > 0) {
			var url = '<?php echo INDEX; ?>?m=operation&c=interactive&v=readbackup_apply';
			Ha.common.ajax(url, 'json', objform.serialize()+'&backtime='+backtime, 'post');
		}else {
			var msg = '<?php echo Lang('not_selected_company_or_server'); ?>';
			if (sid > 0 && player == '') {
				msg = '<?php echo Lang('please_input_player_name'); ?>';
			}
			Ha.notify.show(msg, '', 'error');
		}
		return false;
	});

	$('#player').on('blur', function() {
		var playername = $.trim($(this).val());
		var cid = $('#cid').val(), sid = $('#sid').val();
		var player_type = $('#player_type').val();
		$('#backlist').parent().show();
		if (playername != '' && cid > 0 && sid > 0) {
			var url = '<?php echo INDEX; ?>?m=operation&c=interactive&v=readbackup_apply';
			var queryData = {sid: sid, cid: cid, key: 'fate', playername: playername, player_type: player_type};
			Ha.common.ajax(url, 'json', queryData, 'get', 'container', function(data){
				if (data.status == 0){
					$('#backlist').empty().append($( "#backlisttpl" ).tmpl( data.list )).show();
				}else {
					$('#backlist').html('<tr><td style="text-align:left"></td></tr>');
				}
			}, 1);
		}else {
			var msg = '<?php echo Lang('not_selected_company_or_server'); ?>';
			if (sid > 0 && player == '') {
				msg = '<?php echo Lang('please_input_player_name'); ?>';
			}
			Ha.notify.show(msg, '', 'error');
		}
	});
	
});

</script>
<script type="text/template" id="backlisttpl">
<tr>
	<td><label><input type="radio" name="backid" value="${id}" data-back-time="${back_time}">${date('Y-m-d H:i:s', back_time)}</label></td>
</tr>
</script>
<form name="post_submit" id="post_submit" method="post">
<div class="frm_cont" id="ajax-submit-area">
    <ul>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('server'); ?>：</span>
           <select name="cid" class="cid ipt_select" id="cid">
           	<option value="0"><?php echo Lang('operation_platform') ?></option>
           </select>
           <select name="sid" class="sid ipt_select" id="sid">
           	<option value="0"><?php echo Lang('all_server') ?></option>
           </select>
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('player') ?>：</span>
			<select name="player_type" id="player_type" class="ipt_select">
				<option value="2"><?php echo Lang('player_nick') ?></option>
				<option value="1"><?php echo Lang('player_name') ?></option>
			</select>
			<input type="text" name="player" id="player" class="ipt_txt">
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('备份列表') ?>：</span>
			<table class="global" width="460" style="min-width:460px;display:none" cellpadding="0" cellspacing="0">
				<tbody id="backlist"></tbody>
			</table>
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('operat_reason') ?>：</span>
			<textarea name="applycontent" id="applycontent" style="width:450px;height:60px;"></textarea>
        </li>
        <li>
			<span class="frm_info">&nbsp;</span>
        </li>	        
    </ul>
</div>
<div class="float_footer">
	<div class="frm_btn"> 
	<input type="hidden" name="doSubmit" value="1">
	<input type="submit" id="btnsubmit" class="btn_sbm" value="<?php echo Lang('submit'); ?>">
	<input type="button" id="btnreset" class="btn_rst" value="<?php echo Lang('close'); ?>" onclick="$.dialog({id:'readBackupManageDialog'}).close();">
	</div>
</div>
</form>