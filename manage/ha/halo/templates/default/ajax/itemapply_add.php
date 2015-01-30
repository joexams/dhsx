<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function(){
	$('#global_companylisttpl').tmpl(global_companylist).appendTo('.cid');
	$('.cid').on('change', function() {
		var cid = $(this).val();
		if (cid > 0 && typeof global_serverlist != 'undefined') {
			$('.sid option[value!="0"]').remove();
			$('#global_serverlisttpl').tmpl(getServerByCid(cid)).appendTo('.sid');
		}
	});
	$('#type').on('change', function() {
		var key = $(this).val(), sid = $('#sid').val();
		if (key != '' && sid > 0) {
			get_source_tmpl(key, sid);
		}
	});

	$('#sid').on('change', function() {
		var sid = $(this).val(), key = $('#type').val();
		if (sid > 0 && key != '') {
			get_source_tmpl(key, sid);
		}else if (sid > 0 && key == ''){
			Ha.notify.show('<?php echo Lang('select_add_item_type_tips'); ?>', '', 'error');
		}
	});

	$('#post_submit').on('submit', function(e) {
		e.preventDefault();
		var obj = $(this), sid = $('#sid').val(), key = $('#type').val();
		if (sid > 0 && key != '') {
			var url = '<?php echo INDEX; ?>?m=operation&c=interactive&v=add_itemapply';
			Ha.common.ajax(url, 'json', obj.serialize(), 'post', 'ajax-submit-area', function(data){
				$( "#applylisttpl" ).tmpl( data.info ).prependTo( "#applylist" ).fadeIn(2000, function(){
					var obj = $(this);
					obj.css('background', '#E6791C');
					setTimeout( function(){	obj.css('background', ''); }, 2000 );
				});
				document.getElementById('post_submit').reset();
			});
		}else {
			Ha.notify.show('<?php echo Lang('select_add_item_type_tips'); ?>', '', 'error');
		}
	});
});

function get_source_tmpl(key, sid) {
	var url = '<?php echo INDEX; ?>?m=manage&c=template&v=public_info';
	Ha.common.ajax(url, 'json', 'sid='+sid+'&key='+key, 'get', 'ajax-submit-area', function(data){
		if (data.status == 0){
			$('#sourcetmpl').html(data.info.content);
			$('#tid').val(data.info.tid);
		}else{
			Ha.notify.show(data.msg, '', 'error');
		}
	}, 1);
}
</script>
<form name="post_submit" id="post_submit" method="post">
<div class="frm_cont" id="ajax-submit-area">
    <ul>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('type'); ?>：</span>
            <select name="type" id="type" class="ipt_select">
             <option value=""><?php echo Lang('type'); ?></option>
             <option value="give_item"><?php echo Lang('item_good'); ?></option>
             <option value="give_soul"><?php echo Lang('soul'); ?></option>
             <option value="system_send_ingot"><?php echo Lang('ingot'); ?></option>
             <option value="increase_player_coins"><?php echo Lang('coins'); ?></option>
             <option value="give_fate"><?php echo Lang('fate'); ?></option>
             <option value="increase_player_skill"><?php echo Lang('skill'); ?></option>
             <option value="increase_player_power"><?php echo Lang('power'); ?></option>
             <option value="increase_player_state_point"><?php echo Lang('player_state_point'); ?></option>
             <option value="set_player_vip_level">VIP</option>
            </select>
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('server') ?>：</span>
			<select name="cid" class="cid ipt_select">
				<option value="0"><?php echo Lang('operation_platform'); ?></option>
			</select>
			<select name="sid" class="sid ipt_select" id="sid">
				<option value="0"><?php echo Lang('all_server'); ?></option>
			</select>
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('player_name') ?>：</span>
			<input type="text" name="playername" id="playername" class="ipt_txt">
        </li>
        <div id="sourcetmpl">

        </div>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('apply_case_content') ?>：</span>
			<textarea name="case_content" id="case_content" style="width:450px;height:60px;"></textarea>
        </li>
        <li>
			<span class="frm_info">&nbsp;</span>
        </li>	        
    </ul>
</div>
<div class="float_footer">
	<div class="frm_btn"> 
	<input type="hidden" name="doSubmit" value="1">
	<input type="submit" id="btnsubmit" class="btn_sbm" value="<?php echo Lang('submit'); ?>">
	<input type="button" id="btnreset" class="btn_rst" value="<?php echo Lang('close'); ?>" onclick="$.dialog({id:'itemApplyManageDialog'}).close();">
	</div>
</div>
</form>