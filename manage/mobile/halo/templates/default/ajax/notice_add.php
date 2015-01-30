<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">

$(function(){
	/**
	 * 运营商
	 */
	if (typeof global_companylist != 'undefined') {
		$('#global_companylisttpl').tmpl(global_companylist).appendTo('.cid');
	}
	/**
	 * 切换平台
	 * @return {[type]} [description]
	 */
	$('.cid').on('change', function(){
		var cid = $(this).val();
		if (cid > 0 && typeof global_serverlist != 'undefined'){
			$('option[value!="0"]', $('.sid')).remove();
			$('#global_serverlisttpl').tmpl(getServerByCid(cid)).appendTo('.sid');
		}else {
			$('option[value!="0"]', $('.sid')).remove();
		}
	});

	/**
	 * 提交
	 * @param  {[type]} e [description]
	 * @return {[type]}   [description]
	 */
	$('#post_submit').on('submit', function(e){
		e.preventDefault();
		var objform = $(this);
		var url = '<?php echo INDEX; ?>?m=operation&c=notice&v=setting';
		Ha.common.ajax(url, 'json', objform.serialize(), 'post', 'ajax-submit-area', function(data){
			if (data.status == 0) {
				Ha.page.getList(Ha.page.pageIndex);
				$.dialog({id: 'noticeManageDialog'}).close();
			}
		});
		return false;
	});

});
</script>

<form name="post_submit" id="post_submit" method="post">
<div class="frm_cont" id="ajax-submit-area">
    <ul>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('company_name'); ?>：</span>
           <select name="cid" class="cid ipt_select">
				<option value="0"><?php echo Lang('company_name') ?></option>
			</select>
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('server') ?>：</span>
			<select multiple name="sid[]" class="sid" style="width:200px;height:250px;">
				
			</select>
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('spokesman') ?>：</span>
			<input type="text" name="spokesman" id="spokesman" class="ipt_txt">
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('notice_content') ?>：</span>
			<input type="text" name="content" id="content" class="ipt_txt">
			<span class="graytitle"><?php echo Lang('max_notice_content_char_length') ?></span>
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('notice_time') ?>：</span>
			<input type="text" name="notice_time" id="notice_time" value="" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" class="ipt_txt" readonly>
        </li> 
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('notice_number') ?>：</span>
			<input type="text" name="notice_number" id="notice_number" class="ipt_txt">
        </li>     
    </ul>
</div>
<div class="float_footer">    
	<div class="frm_btn"> 
    <input type="hidden" name="doSubmit" value="1">
	<input type="hidden" name="nid" id="nid" value="<?php echo $data['info']['nid'] ?>">
    <input type="submit" id="btnsubmit" class="btn_sbm" value="<?php echo Lang('submit'); ?>">
	<input type="button" id="btnreset" class="btn_rst" value="<?php echo Lang('close'); ?>" onclick="$.dialog({id:'noticeManageDialog'}).close();">
	</div>
</div>
</form>