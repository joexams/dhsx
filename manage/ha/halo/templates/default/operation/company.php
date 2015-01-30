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
	 * 获取对应信息
	 * @return {[type]} [description]
	 */
	$('#cid').on('change', function(){
		var obj = $(this), cid = $(this).val();
		if (cid > 0){
			var url = '<?php echo INDEX; ?>?m=develop&c=company&v=ajax_setting_info';
			Ha.common.ajax(url, 'json', {cid: cid, format: 'json'}, 'get', 'container', function(data){
				 if (data.status == 0){
			       		$('#game_text').val(data.info.game_text);
			       		for(var i=0; i<8; i++){
			       			if (data.info.link[i] != ''){
			       				$('#submit_area').find('input[name^="link"]').eq(i).val(data.info.link[i]);
			       			}else {
			       				$('#submit_area').find('input[name^="link"]').eq(i).val('');
			       			}
			       		}
			       }else {
			       		Ha.notify.show(data.msg, '', 'error');
			       }
			}, 1);
		}else {
			$('#game_text').val('');
			for(var i=0; i<8; i++){
				$('#submit_area').find('input[name^="link"]').eq(i).val('');
			}
		}
	});

	$('#post_submit').on('submit', function(e){
		e.preventDefault();
		var objform = $(this), cid = $('#cid').val();
		if (cid > 0){
			var url = '<?php echo INDEX; ?>?m=develop&c=company&v=setting';
			Ha.common.ajax(url, 'json', objform.serialize()+'&cid='+cid, 'POST', 'container');
		}else {
			Ha.notify.show('请选择所属的运营商', '', 'error');
		}
	});
});
</script>


<h2><span id="tt"><?php echo Lang('company_setting'); ?></span></h2>
<div class="container" id="container">
	<div class="frm_cont" id="submit_area">
		<form id="post_submit" method="post">
	    <ul>
	        <li>
	            <span class="frm_info"><em>*</em><?php echo Lang('company_title') ?>：</span>
	            <select name="cid" id="cid" class="ipt_select">
					<option value="0"><?php echo Lang('company_name') ?></option>
				</select>
	        </li>
	       <li>
	            <span class="frm_info"><?php echo Lang('company_game_description') ?>：</span>
	            <input type="text" name="game_text" id="game_text" value="" class="ipt_txt_xl">
	        </li>
	       <li>
	            <span class="frm_info"><?php echo Lang('company_website') ?>：</span>
	            <input type="text" name="link[]" id="website" value="" class="ipt_txt_xl">
	        </li>
	       <li>
	            <span class="frm_info"><?php echo Lang('company_bbs_url') ?>：</span>
	            <input type="text" name="link[]" id="bbs_url" value="" class="ipt_txt_xl">
	        </li>
	       <li>
	            <span class="frm_info"><?php echo Lang('company_pay_url') ?>：</span>
	            <input type="text" name="link[]" id="pay_url" value="" class="ipt_txt_xl">
	        </li>
	       <li>
	            <span class="frm_info"><?php echo Lang('company_vip_url') ?>：</span>
	            <input type="text" name="link[]" id="vip_url" value="" class="ipt_txt_xl">
	        </li>
	       <li>
	            <span class="frm_info"><?php echo Lang('company_gm_url') ?>：</span>
	            <input type="text" name="link[]" id="gm_url" value="" class="ipt_txt_xl">
	        </li>
	       <li>
	            <span class="frm_info"><?php echo Lang('company_fav_title') ?>：</span>
	            <input type="text" name="link[]" id="fav_title" value="" class="ipt_txt_l">
	            <label><?php echo Lang('company_fav_title_tips') ?></label>
	        </li>
	       <li>
	            <span class="frm_info"><?php echo Lang('company_fav_url') ?>：</span>
	            <input type="text" name="link[]" id="fav_url" value="" class="ipt_txt_l">
	            <label><?php echo Lang('company_fav_url_tips') ?></label>
	        </li>
	       <li>
	            <span class="frm_info"><?php echo Lang('company_fcm_url') ?>：</span>
	            <input type="text" name="link[]" id="fcm_url" value="" class="ipt_txt_l">
	            <label><?php echo Lang('company_fcm_url_tips') ?></label>
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