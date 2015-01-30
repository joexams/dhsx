<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function() {
	/**
	 * 运营平台
	 */
	setTimeout(function() {
		if (typeof global_companylist != 'undefined') {
			$('#cid option[value!=0]').remove();
			$('#global_companylisttpl').tmpl(global_companylist).appendTo('#cid');
		}
	}, 250);
	
	$('#cid').on('change', function(){
		var cid = $(this).val();
		if (cid > 0 && global_serverlist){
			$('#sid option').remove();
			$('#global_serverlisttpl').tmpl(getServerByCid(cid)).appendTo('#sid');
		}else {
			$('#sid option').remove();
		}
	});

	$('#post_submit').on('submit', function(e) {
		e.preventDefault();
		var objform = $(this), cid = $('#cid').val(), sid = $('#sid').val();
		if (cid > 0 && sid.length > 0) {
			var url = '<?php echo INDEX; ?>?m=operation&c=source&v=game';
			Ha.common.ajax(url, 'json', objform.serialize(), 'POST', 'container', function(data){
				document.getElementById('post_submit').reset();
				$('#sid option').remove();
			});
		}else {
			var msg = '<?php echo Lang('not_selected_company_or_server'); ?>';
			Ha.notify.show(msg, '', 'error');
		}
		return false;
	});
});
</script>


<h2><span id="tt"><?php echo Lang('game_setting'); ?></span></h2>
<div class="container" id="container">
	<div class="frm_cont" id="submit_area">
		<form id="post_submit" method="post">
	    <ul>
	        <li>
	            <span class="frm_info"><em>*</em><?php echo Lang('company_platform') ?>：</span>
	            <select name="cid" id="cid" class="ipt_select">
					<option value="0"><?php echo Lang('company_name') ?></option>
				</select>
	        </li>
	        <li>
	            <span class="frm_info"><em>*</em><?php echo Lang('server') ?>：</span>
	            <select multiple name="sid[]" id="sid" style="width:190px;height:250px;">
					
				</select>
	        </li>
	        <li>
	            <span class="frm_info">BOSS设置：</span>
	            <select name="world_boss_id" id="world_boss_id" class="ipt_select">
					<option value=""><?php echo Lang('does_not_operation'); ?></option>
					<option value="1">擎天木</option>
					<option value="2">赤炎兽</option>
				</select>
				<select name="bossoptype" class="ipt_select">
					<option value="">（开关）<?php echo Lang('does_not_operation'); ?></option>
					<option value="1">开</option>
					<option value="2">关</option>
				</select>
	        </li>
	        <li>
	            <span class="frm_info">BOSS<?php echo Lang('level'); ?>：</span>
	            <input type="text" name="level" value="0" class="ipt_txt">
	        </li>
	        <li>
	            <span class="frm_info">帮派战：</span>
	            <select name="faction_war_id" class="ipt_select">
					<option value="0"><?php echo Lang('does_not_operation'); ?></option>
					<option value="1">白虎殿</option>
				</select>
				<select name="factionoptype" class="ipt_select">
					<option value="0">（开关）<?php echo Lang('does_not_operation'); ?></option>
					<option value="1">开</option>
					<option value="2">关</option>
				</select>
	        </li>
	        <li>
	            <span class="frm_info">阵营战设置：</span>
				<select name="campoptype" class="ipt_select">
					<option value="0">（开关）<?php echo Lang('does_not_operation'); ?></option>
					<option value="1">开</option>
					<option value="2">关</option>
				</select>
	        </li>
	        <li>
	            <span class="frm_info">魔王试炼设置：</span>
				<select name="optype" class="ipt_select">
					<option value="0">（开关）<?php echo Lang('does_not_operation'); ?></option>
					<option value="1">开</option>
					<option value="2">关</option>
				</select>
	        </li>
	       <li>
	            <span class="frm_info">&nbsp;</span>
	            <input type="hidden" name="doSubmit" value="1">
	            <input type="submit" id="btnsubmit" class="btn_sbm" value="<?php echo Lang('submit'); ?>">
				<input type="reset" id="btnreset" class="btn_rst" value="<?php echo Lang('reset'); ?>">
	        </li>	        
	    </ul>
		</form>
	</div>
</div>