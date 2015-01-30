<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(document).ready(function(){
	setTimeout(function() {
		if (typeof global_companylist != 'undefined') {
			$('option[value!="0"]', $('#cid')).remove();
			$('#global_companylisttpl').tmpl(global_companylist).appendTo('#cid');
		}
	}, 250);

	$('#cid').on('change', function(){
		var cid = $(this).val();
		if (cid > 0 && typeof global_serverlist != 'undefined') {
			$('option[value!="0"]', $('#sid')).remove();
			$('#global_serverlisttpl').tmpl(getServerByCid(cid)).appendTo('#sid');
		}else {
			$('option[value!="0"]', $('#sid')).remove();
		}
	});
	/**
	 * 提交
	 * @param  {[type]} e [description]
	 * @return {[type]}   [description]
	 */
	$('#post_submit').on('submit', function(e){
		e.preventDefault();
		var objform = $(this), sid = $('#sid').val(), cid = $('#cid').val(), playername = objform.find('input[name="playername"]').val();
		if (sid > 0 && cid > 0 && playername != ''){
			var url = '<?php echo INDEX; ?>?m=operation&c=premium&pay';
			Ha.common.ajax(url, 'json', objform.serialize(), 'post', 'container');
		}else {
			Ha.notify.show('带*都需要填写哦~', '','error');
		}
	});
});
</script>


<h2><span id="tt"><?php echo Lang('pay_premium'); ?></span></h2>
<div class="container" id="container">
	<div class="frm_cont" id="submit_area">
		<form id="post_submit" method="post">
	    <ul>
	        <li>
	            <span class="frm_info"><em>*</em><?php echo Lang('server') ?>：</span>
	            <select name="cid" id="cid" class="ipt_select">
					<option value="0"><?php echo Lang('operation_platform') ?></option>
				</select>
				<select name="sid" id="sid" class="ipt_select">
					<option value="0"><?php echo Lang('all_server') ?></option>
				</select>
	        </li>
	        <li>
	            <span class="frm_info"><em>*</em><?php echo Lang('player_name') ?>：</span>
	            <input type="text" name="playername" id="playername" class="ipt_txt">
	        </li>
	        <li>
	            <span class="frm_info"><em>*</em><?php echo Lang('pay_ingot') ?>：</span>
	            <input type="text" name="ingot" id="ingot" class="ipt_txt_s">
	        </li>
	        <li>
	            <span class="frm_info"><em>*</em><?php echo Lang('pay_money') ?>：</span>
	            <input type="text" name="amout" id="amout" class="ipt_txt_s">
	        </li>
	        <li>
	            <span class="frm_info"><em>*</em><?php echo Lang('pay_order_no') ?>：</span>
	            <input type="text" name="oid" id="oid" class="ipt_txt">
	        </li>
	        <li>
	            <span class="frm_info"><em>*</em><?php echo Lang('pay_time') ?>：</span>
	            <input type="text" name="dtime_unix" id="dtime_unix" class="ipt_txt" value="<?php echo date('Y-m-d H:i:s', time()); ?>">
	        </li>
	       <li>
	            <span class="frm_info">&nbsp;</span>
	            <input type="submit" id="btnsubmit" class="btn_sbm" value="<?php echo Lang('submit'); ?>">
				<input type="reset" class="btn_rst" value="<?php echo Lang('reset'); ?>">
	        </li>	        
	    </ul>
		</form>
	</div>
</div>