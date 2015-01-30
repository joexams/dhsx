<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(document).ready(function(){
	/**
	 * 运营商
	 */
	if (typeof global_companylist != 'undefined') {
		$('#global_companylisttpl').tmpl(global_companylist).appendTo('#cid');
	}
	/**
	 * 切换平台
	 * @return {[type]} [description]
	 */
	$('#cid').on('change', function(){
		var cid = $(this).val();
		if (cid > 0 && typeof global_serverlist != 'undefined'){
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
		var objform = $(this), cid = $('#cid').val(), sid = $('#sid').val();
		if (cid > 0 && sid != null) {
			var url = '<?php echo INDEX; ?>?m=operation&c=source&v=hire_role';
			Ha.common.ajax(url, 'json', objform.serialize(), 'POST', 'container', function(data){
				document.getElementById('post_submit').reset();
			});
		}else {
			Ha.notify.show('<?php echo Lang('required_fields_mustbe_field'); ?>', '', 'error');
		}
		return false;
	});
});
</script>

<script type="text/template" id="serverchklisttpl">
<div>
	<li><input type="checkbox" name="sid[]" value="${sid}">${name}</li>
</div>
</script>
<h2><span id="tt"><?php echo Lang('publish_hire_role'); ?></span></h2>
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
	            <select multiple name="sid[]" id="sid" style="width:190px;height:300px;">
					
				</select>
	        </li>
	        <li>
	            <span class="frm_info"><em>*</em><?php echo Lang('partner')?>：</span>
	            <label>
	            <?php foreach ($typelist as $key => $value) { ?>
					 <input id="partners" type="checkbox" name="partners[]" value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?>
				<?php } ?>
				</label>
	        </li>
	        <li>
	        	<span class="frm_info"><em>*</em><?php echo Lang('between_date')?>：</span>
	        	<input id="starttime" name="starttime" class="ipt_txt" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd 00:00:00'})" type="text" value=""  style="width:125px;"> - 
				<input id="endtime" name="endtime" class="ipt_txt" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd 23:59:59'})" type="text" value=""  style="width:125px;">
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