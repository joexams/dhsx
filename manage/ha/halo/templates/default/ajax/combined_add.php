<script type="text/javascript">
$(function(){
	$('#post_submit').on('submit', function(e) {
		e.preventDefault();
		var objform = $(this), cid = $('#cid').val(), sid = $('#sid').val();
		sid = sid == null ? '' : sid;
		if (cid > 0 && sid.length == 2){
			var url = '<?php echo INDEX; ?>?m=develop&c=server&v=combine';
			Ha.common.ajax(url, 'json', objform.serialize(), 'post', 'ajax-submit-area', function(data){
				if (data.status == 0) {
					if (data.editflag == 1){
						for(var key in combinelist) {
							if (combinelist[key].id == data.info.id) {
								combinelist[key] = data.info;
								$('#combinedlist').empty();
								$( "#combinedlisttpl" ).tmpl( combinelist ).appendTo( "#combinedlist" );
								break;
							}
						}
					}else {
						$( "#combinedlisttpl" ).tmpl( data.info ).prependTo( "#combinedlist" ).fadeIn(2000, function(){
							var obj = $(this);
							obj.css('background', '#E6791C');
							setTimeout( function(){	obj.css('background', ''); }, ( 2000 ) );
						});
					}
				}
			});
		}else {
			var msg = '<?php echo Lang('not_selected_company_or_server'); ?>';
			if (sid.length > 2) {
				msg = '只能选择2台服务器进行合服！';
			}
			Ha.notify.show(msg, '', 'errror');
		}
	});
});
</script>

<form name="post_submit" id="post_submit" method="post">
<div class="frm_cont" id="ajax-submit-area">
    <ul>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('server_date'); ?>：</span>
            <input type="text" name="opendate" id="opendate" class="ipt_txt" value="<?php echo date('Y-m-d H:i:s') ?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" readonly>
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('server') ?>：</span>
			<select name="cid" class="cid ipt_select">
				<option value="0"><?php echo Lang('operation_platform'); ?></option>
			</select>
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('server_list'); ?>：</span>
            <select name="sid[]" multiple="multiple" id="sid" style="width:300px;height:250px;">

            </select>
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('remark') ?>：</span>
			<textarea name="content" id="content" style="width:300px;height:60px;"></textarea>
        </li>
        <li>
			<span class="frm_info">&nbsp;</span>
        </li>	        
    </ul>
</div>
<div class="float_footer">
	<div class="frm_btn"> 
	<input type="hidden" name="doSubmit" value="1">
	<input type="hidden" id="combineid" name="combineid" value="0">
	<input type="submit" id="btnsubmit" class="btn_sbm" value="<?php echo Lang('submit'); ?>">
	<input type="button" id="btnreset" class="btn_rst" value="<?php echo Lang('close'); ?>" onclick="$.dialog({id:'combinedManageDlg'}).close();">
	</div>
</div>
</form>