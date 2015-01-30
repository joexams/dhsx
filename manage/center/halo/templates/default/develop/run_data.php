<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function(){
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
	$('#btnsubmit').on('click', function(){
		var sid = $('#sid').val();cid = $('#cid').val();sql = $('#sql').val();
		alert(sid);
		if (cid==0 || sid.length==0 || sql == ''){
			alert("还有资料未填");
			return false;
		}
	});	
});
</script>

<div id="tipsBar" class="msg_tips"> 
    <i class="i_tips"></i>
    <p id="p_tips"><?php echo Lang('select_need_columns')?></p>
</div>
<h2><span id="tt"><?php echo Lang('run_data'); ?></span></h2>
<div class="container" id="container">
<div class="frm_cont" id="submit_area">
		<form name="post_submit" id="post_submit" method="POST" action="<?php echo INDEX; ?>?m=develop&c=server&v=run_data">
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
	    	    <span class="frm_info"><em>*</em>SQL:</span>
	    	    <textarea name="sql" id="sql" class="ipt_textarea" style="width:80%;height:100px"></textarea>
	    	</li>
	        <li>
	            <span class="frm_info">&nbsp;</span>
	            <input type="hidden" name="doSubmit" value="1">
				<input type="submit" id="btnsubmit" class="btn_sbm" value="<?php echo Lang('submit'); ?>">
				<input type="reset" id="btnreset" class="btn_rst"  value="<?php echo Lang('reset'); ?>">
	        </li>
	    </ul>
		</form>
	</div>
</div>