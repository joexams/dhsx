<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function() {
	/**
	 * 运营平台
	 */
	setTimeout(function() {
		if (typeof global_companylist != 'undefined') {
			$('#global_companylisttpl').tmpl(global_companylist).appendTo('#cid');
		}
	}, 250);

	$('#cid').on('change', function(){
		var cid = $(this).val();
		if (cid > 0 && typeof global_serverlist != 'undefined'){
			$('#sid option[value!="0"]').remove();
			$('#global_serverlisttpl').tmpl(getServerByCid(cid)).appendTo('#sid');
		}else {
			$('#sid option[value!="0"]').remove();
		}
	});

	$('#get_submit').on('submit', function(e) {
		e.preventDefault();
		var objform = $(this), cid = $('#cid').val(), sid = $('#sid').val();
		if (cid > 0 && sid > 0) {
			var sname = $('#sid option:selected').text();
			sname = encodeURIComponent(sname);
			location.href = '<?php echo WEB_URL.INDEX;?>?m=server&c=get&v=export_player&sname='+sname+'&'+objform.serialize();
		}else {
			Ha.notify.show('<?php echo Lang('not_selected_company_or_server'); ?>', '', 'error');
		}
	});
});
</script>

<h2><span id="tt"><?php echo Lang('export_player'); ?></span></h2>
<div class="container" id="container">
	<div class="frm_cont" id="submit_area">
		<form name="get_submit" id="get_submit" method="get">
	    <ul>
	        <li>
	            <span class="frm_info"><em>*</em><?php echo Lang('server') ?>：</span>
	           <select name="cid" id="cid" class="ipt_select" style="width:130px;">
					<option value="0"><?php echo Lang('operation_platform') ?></option>
				</select>
				<select name="sid" id="sid" class="ipt_select">
					<option value="0"><?php echo Lang('all_server') ?></option>
				</select>
	        </li>
	        <li>
	            <span class="frm_info"><em>*</em><?php echo Lang('create_role') ?>：</span>
				<select name="ctype" id="ctype" class="ipt_select">
					<option value="1"><?php echo Lang('already_create'); ?></option>}
					<option value="2"><?php echo Lang('no_create'); ?></option>
					<option value="0"><?php echo Lang('all'); ?></option>
				</select>
	        </li>
	        <li>
	            <span class="frm_info"><em>*</em><?php echo Lang('channel') ?>：</span>
				<select name="source" id="source" class="ipt_select">
					<option value=""><?php echo Lang('all'); ?></option>
				</select>
	        </li>
	        <li>
	            <span class="frm_info"><em>*</em><?php echo Lang('register_date') ?>：</span>
				<input type="text" name="starttime" class="ipt_txt_s" readonly onclick="WdatePicker()">
				-
				<input type="text" name="endtime" class="ipt_txt_s" readonly onclick="WdatePicker()">
	        </li>
	        <li>
	            <span class="frm_info">VIP<?php echo Lang('level') ?>：</span>
				<input type="text" name="start_vip_level" class="ipt_txt_s">
				-
				<input type="text" name="end_vip_level" class="ipt_txt_s">
	        </li>
	        <li>
	            <span class="frm_info"><?php echo Lang('role').Lang('level'); ?>：</span>
				<input type="text" name="minlevel" class="ipt_txt_s">
				-
				<input type="text" name="maxlevel" class="ipt_txt_s">
	        </li>
	        <li>
	            <span class="frm_info"><em>*</em><?php echo Lang('player_name'); ?>：</span>
				<textarea name="username" style="width:200px;height:200px;" class="ipt_textarea"></textarea>
				<span class="graytitle"><?php echo Lang('server_game_url_tips'); ?></span>
	        </li>
           <li>
                <span class="frm_info">&nbsp;</span>
                <input type="hidden" name="doSubmit" value="1">
                <input type="submit" id="btnsubmit" class="btn_sbm" value="<?php echo Lang('sure_export'); ?>">
    			<input type="reset" id="btnreset" class="btn_rst" value="<?php echo Lang('reset'); ?>">
            </li>	        
        </ul>
    	</form>
    </div>
</div>