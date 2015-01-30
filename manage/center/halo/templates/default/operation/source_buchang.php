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
			var url = '<?php echo INDEX; ?>?m=operation&c=source&v=buchang';
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


<h2><span id="tt"><?php echo Lang('fa_fang_bu_chang'); ?></span></h2>
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
	            <span class="frm_info"><?php echo Lang('礼包名称')?>：</span>
	            <input type="text" name="msg" value="" class="ipt_txt">
	        </li>
	        <li>
	            <span class="frm_info"><?php echo Lang('base_info')?>：</span>
	            <input type="text" name="verinfo" value="" class="ipt_txt">
	        </li>
	        <li>
	            <span class="frm_info"><?php echo Lang('最小等级')?>：</span>
	            <input type="text" name="minlv" value="" class="ipt_txt_s">
	        </li>
	        <li>
	            <span class="frm_info"><?php echo Lang('最大等级')?>：</span>
	            <input type="text" name="maxlv" value="" class="ipt_txt_s">
	        </li>
	        
	        <li>
	            <span class="frm_info"><?php echo Lang('元宝')?>：</span>
	            <input type="text" name="ingot" value="" class="ipt_txt_s">
	        </li>
	        <li>
	            <span class="frm_info"><?php echo Lang('铜钱')?>：</span>
	            <input type="text" name="coin" value="" class="ipt_txt_s">
	        </li>
	        <li>
	            <span class="frm_info"><?php echo Lang('声望')?>：</span>
	            <input type="text" name="fame" value="" class="ipt_txt_s">
	        </li>
	        <li>
	            <span class="frm_info"><?php echo Lang('阅历')?>：</span>
	            <input type="text" name="skill" value="" class="ipt_txt_s">
	        </li>
	        <li>
	            <span class="frm_info"><?php echo Lang('功勋')?>：</span>
	            <input type="text" name="feat" value="" class="ipt_txt_s">
	        </li>
	        <li>
	            <span class="frm_info"><?php echo Lang('物品')?>1：</span>
	            <select name="item1" class="ipt_select">
	            	<option value="0"><?php echo Lang('请选择'); ?></option>
					<option value="1217"><?php echo Lang('bao_zi'); ?></option>
					<option value="347"><?php echo Lang('huang_yu_pai'); ?></option>
				</select>
				<?php echo Lang('数量')?>：
				<input type="text" name="num1" value="" class="ipt_txt_s">
	        </li>
	        <li>
	            <span class="frm_info"><?php echo Lang('物品')?>2：</span>
	            <select name="item2" class="ipt_select">
	            	<option value="0"><?php echo Lang('请选择'); ?></option>
					<option value="1217"><?php echo Lang('bao_zi'); ?></option>
					<option value="347"><?php echo Lang('huang_yu_pai'); ?></option>
				</select>
				<?php echo Lang('数量')?>：
				<input type="text" name="num2" value="" class="ipt_txt_s">
	        </li>
	        <li>
	            <span class="frm_info"><?php echo Lang('物品')?>3：</span>
	            <select name="item3" class="ipt_select">
	            	<option value="0"><?php echo Lang('请选择'); ?></option>
					<option value="1217"><?php echo Lang('bao_zi'); ?></option>
					<option value="347"><?php echo Lang('huang_yu_pai'); ?></option>
				</select>
				<?php echo Lang('数量')?>：
				<input type="text" name="num3" value="" class="ipt_txt_s">
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