<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function() {
	/**
	 * 运营平台
	 */
	if (typeof global_companylist != 'undefined') {
		$('#global_companylisttpl').tmpl(global_companylist).appendTo('.cid');
	}


	$('#cid').on('change', function(){
		var cid = $(this).val();
		if (cid > 0 && typeof global_serverlist != 'undefined'){
			$('#sid option[value!="0"]').remove();
			$('#global_serverlisttpl').tmpl(getServerByCid(cid)).appendTo('#sid');
		}else {
			$('#sid option[value!="0"]').remove();
		}
	});
	/**
	 * 提交
	 * @param  {[type]} e [description]
	 * @return {[type]}   [description]
	 */
	$('#post_submit').on('submit', function(e) {
		e.preventDefault();
		var cid = $('#cid1').val(), tblname = $.trim($('#table_name').val()), comment = $.trim($('#comment').val());
		var objform = $(this);
		if (cid > 0 && tblname != '' && comment != '') {
			var url = '<?php echo INDEX; ?>?m=report&c=coupon&v=add_active';
			Ha.common.ajax(url, 'json', objform.serialize(), 'post', 'container', function(data){
				document.getElementById('post_submit').reset();
			});
		}else {
			Ha.notify.show('<?php echo Lang('msg_error')?>', '', 'error');
		}
	});
	
	/**
	 * 追加
	 * @param  {[type]} e [description]
	 * @return {[type]}   [description]
	 */
	$('#again_post_submit').on('submit', function(e) {
		e.preventDefault();
		var cid = $('#cid2').val(), tblname = $.trim($('#again_table').val()), num = isNaN(parseInt($('#again_num').val())) ? 0 : parseInt($('#again_num').val());
		var objform = $(this);
		if (cid > 0 && tblname != '0' && num > 0) {
			var url = '<?php echo INDEX; ?>?m=report&c=coupon&v=add_again_active';
			Ha.common.ajax(url, 'json', objform.serialize(), 'post', 'container', function(data){
				document.getElementById('again_post_submit').reset();
			});
		}else {
			Ha.notify.show('<?php echo Lang('msg_error')?>', '', 'error');
		}
	});

	/**
	 * 切换
	 * @return {[type]} [description]
	 */
	$('.first_level_tab').on('click', 'a.coupon', function(){
		$('.current', $('.first_level_tab')).removeClass('current');
		$(this).parent().addClass('current');
		var index = $(this).attr('data-type');
		$('.frm_cont').hide();
		$('#ajax-submit-area'+index).show();
	});
});
</script>

<h2><span id="tt"><?php echo Lang('active_coupon'); ?></span></h2>
<div class="container" id="container">
	<div class="speed_result">
	    <div class="mod_tab_title first_level_tab">
	    	<ul>
	    		<li class="current"><a href="javascript:void(0);" data-type="0" class="coupon"><?php echo Lang('new_coupon')?></a></li>
	    		<li><a href="javascript:void(0);" data-type="1" class="coupon"><?php echo Lang('addto_coupon')?></a></li>
	    	</ul>
	    </div>
	</div>

	<div class="frm_cont" id="ajax-submit-area0">
		<form name="post_submit" id="post_submit" method="post">
	    <ul>
	        <li>
	            <span class="frm_info"><em>*</em><?php echo Lang('company_platform'); ?>：</span>
	            <select name="cid" class="cid ipt_select" id="cid1">
					<option value="0"><?php echo Lang('operation_platform') ?></option>
				</select>
	        </li>
	        <li>
	            <span class="frm_info"><em>*</em><?php echo Lang('table_end'); ?>：</span>
	            <input type="text" name="table_name" id="table_name" class="ipt_txt" value="qq" onkeyup="$('#tblsuffix').html(this.value)"><span style="margin-left:5px;" class="graytitle"><?php echo Lang('corresponding_table_name')?>code_party_<strong class="greentitle" id="tblsuffix">qq</strong></span>
	        </li>
	        <li>
	            <span class="frm_info"><em>*</em><?php echo Lang('table_desc'); ?>：</span>
	            <input type="text" name="comment" id="comment" class="ipt_txt" value="">
	        </li>
	        <li>
	            <span class="frm_info"><em>*</em><?php echo Lang('generate_num'); ?>：</span>
	            <input type="text" name="num" id="num" class="ipt_txt" value=""><span style="margin-left:5px;"  class="graytitle"><?php echo Lang('oncetime_cannot_one_hundred_thousand')?></span>
	        </li>
	        <li>
	            <span class="frm_info">&nbsp;</span>
				<input type="submit" id="btnsubmit" class="btn_sbm" value="<?php echo Lang('submit'); ?>">
				<input type="reset" id="btnreset" class="btn_rst"  value="<?php echo Lang('reset'); ?>">
	        </li>
	    </ul>
		</form>
	</div>

	<div class="frm_cont" id="ajax-submit-area1" style="display:none">
		<form name="again_post_submit" id="again_post_submit" method="post">
	    <ul>
	        <li>
	            <span class="frm_info"><em>*</em><?php echo Lang('company_platform'); ?>：</span>
	            <select name="cid" class="cid ipt_select" id="cid2">
					<option value="0"><?php echo Lang('operation_platform') ?></option>
				</select>
	        </li>
	        <li>
	            <span class="frm_info"><em>*</em><?php echo Lang('active'); ?>：</span>
	            <select name="table_name" id="again_table" class="ipt_select">
					<option value="0"><?php echo Lang('please_select_active'); ?></option>
					<?php foreach ($activelist as $key => $value) {
						echo '<option value="'.$value['table_name'].'">'.$value['table_comment'].'</option>';
					} ?>
				</select>
	        </li>
	        <li>
	            <span class="frm_info"><em>*</em><?php echo Lang('table_desc'); ?>：</span>
	            <input type="text" name="comment" id="comment" class="ipt_txt" value="">
	        </li>
	        <li>
	            <span class="frm_info"><em>*</em><?php echo Lang('add_number')?>：</span>
	            <input type="text" name="again_num" id="again_num" class="ipt_txt" value=""><span style="margin-left:5px;"  class="graytitle"><?php echo Lang('oncetime_cannot_one_hundred_thousand')?></span>
	        </li>
	        <li>
	            <span class="frm_info">&nbsp;</span>
				<input type="submit" id="again_btnsubmit" class="btn_sbm" value="<?php echo Lang('submit'); ?>">
				<input type="reset" id="again_btnreset" class="btn_rst"  value="<?php echo Lang('reset'); ?>">
	        </li>
	    </ul>
		</form>
	</div>

</div>