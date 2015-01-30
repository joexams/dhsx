<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
	/**
	 * 获取平台
	 */
	setTimeout(function() {
		if (typeof global_companylist != 'undefined') {
			$('#global_companylisttpl').tmpl(global_companylist).appendTo('#cid');
		}
	}, 250);
	/**
	 * 改变平台
	 * @return {[type]} [description]
	 */
	$('#cid').on('change', function(){
		var cid = $(this).val();
		if (cid > 0 && typeof global_serverlist != 'undefined') {
			$('option[value!="0"]', $('.sid')).remove();
			$('#global_serverlisttpl').tmpl(getServerByCid(cid)).appendTo('.sid');
		}else {
			$('option[value!="0"]', $('.sid')).remove();
		}
	});
	/**
	 * 提交
	 * @return {[type]} [description]
	 */
	$('#post_submit').on('submit', function(e){
		e.preventDefault();
		var objform = $(this), source_sid = $('#source_sid').val(), target_sid = $('#target_sid').val(), cid = $('#cid').val(), playername = $('#playername').val(), oid = $('#oid').val();
		if (source_sid > 0 && target_sid > 0 && cid > 0 && playername != ''&& oid != ''){
			var url = '<?php echo INDEX; ?>?m=operation&c=premium&v=transfer';
			Ha.common.ajax(url, 'json', objform.serialize(), 'post', 'container');
		}else {
			Ha.notify.show('<?php echo Lang('required_fields_mustbe_field');?>', '','error');
		}
	});
</script>
<h2><span id="tt"><?php echo Lang('order_transfer'); ?></span></h2>
<div class="container" id="container">
	<div class="frm_cont" id="submit_area">
		<form name="post_submit" id="post_submit" method="post">
			<input type="hidden" name="doSubmit" value="1">
			<ul>
	        <li>
	            <span class="frm_info"><em>*</em><?php echo Lang('server') ?>：</span>
	            <select name="cid" id="cid" class="ipt_select">
					<option value="0"><?php echo Lang('operation_platform') ?></option>
				</select>
	        </li>
	        <li>
	        	<span class="frm_info">&nbsp;</span>
	        	<select name="source_sid" id="source_sid" class="ipt_select sid">
					<option value="0"><?php echo Lang('all_server') ?></option>
				</select>
				->
				<select name="target_sid" id="target_sid" class="ipt_select sid">
					<option value="0"><?php echo Lang('all_server') ?></option>
				</select>
	        </li>
	        <li>
	            <span class="frm_info"><em>*</em><?php echo Lang('pay_order_no') ?>：</span>
	            <input type="text" name="oid" id="oid" class="ipt_txt">
	            ->
	            <em>*</em><?php echo Lang('player_name') ?>：
	            <input type="text" name="playername" id="playername" class="ipt_txt">
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