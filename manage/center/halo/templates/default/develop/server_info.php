<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function(){
	//添加
	$('#post_pop_submit').on('submit', function(e){
		e.preventDefault();
		var objform = $(this);
		var url = '<?php echo INDEX; ?>?m=develop&c=server&v=setting';
		Ha.common.ajax(url, 'json', objform.serialize(), 'post');
		$.dialog({id:'dialog_s_setting'}).close();
	});

	//测试数据库连接
	$('#submit_area').on('click', 'a.test_db_connect', function(){
		var dbhost = $('#db_server').val(), dbroot = $('#db_root').val(), dbname = $('#db_name').val(), dbpwd = $('#db_pwd').val();
		var url = '<?php echo INDEX; ?>?m=develop&c=server&v=test_db_connect';
		var queryData = 'dbhost='+dbhost+'&dbroot='+dbroot+'&dbname='+dbname+'&dbpwd='+dbpwd;
		Ha.common.ajax(url, 'json', queryData, 'get', 'container', function(data){
			var alertclassname = '', time = 2;
		  	switch (data.status){
		  		case 0: alertclassname = 'greentitle'; break;
		  		case 1: alertclassname = 'redtitle'; break;
		  	}
		  	$('#connect_tips').attr('class', alertclassname);
		  	$('#connect_tips').html(data.msg);
		}, 1);
	});
	$("#reset").on('click',function(){
		$("#combined_to").attr("value",0);
		$("#api_server").attr("value",0);
		$("#api_port").attr("value",0);
		$("#db_server").attr("value",0);checked
		$("#is_use").attr("checked",true);
	});
});


</script>


<form name="post_pop_submit" id="post_pop_submit" method="post">
<div class="frm_cont" id="submit_area">
    <ul>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('server_o_name'); ?>：</span>
            <input type="text" name="name" id="name" value="<?php echo $data['info']['name'] ? $data['info']['name'] : $data['slug']; ?>" onblur="$('#db_name').val('gamedb_'+ this.value);" class="ipt_txt">
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('server_name'); ?>：</span>
            <input type="text" name="o_name" id="o_name" value="<?php echo $data['info']['o_name'] ?>" class="ipt_txt">
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('server_game_url'); ?>：</span>
            <textarea name="server" id="server" style="width:300px;height:60px;"><?php echo $data['info']['server']; ?></textarea>
            <a href="javascript:void(0);" onmouseover="Ha.common.showItemTips('<?php echo Lang('server_game_url_tips'); ?>',this,{'id': 'game_url_tips'})"><i class="i_help"></i></a>
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('server_date'); ?>：</span>
            <input type="text" name="open_date" id="open_date" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:00:00'})" value="<?php echo $data['info']['open_date']; ?>" class="ipt_txt">
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('server_version'); ?>：</span>
			<select name="server_ver" id="server_ver" class="ipt_select">
				<option value="0"><?php echo Lang('no_setting'); ?></option>
				<?php echo $data['versionstring'] ?>
			</select>
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('server_combined'); ?>：</span>
			<select name="combined_to" id="combined_to" class="ipt_select">
				<?php if ($data['info']['combined_to'] > 0) { ?>
				<option value="<?php echo $data['info']['combined_to']; ?>"><?php echo $data['info']['combined_name'].'-'.$data['info']['combined_o_name']; ?></option>
				<?php } ?>
				<option value="0"><?php echo Lang('server_no_combined'); ?></option>
			</select>
            <a href="javascript:void(0);" onmouseover="Ha.common.showItemTips('<?php echo Lang('server_combined_tips'); ?>',this,{'id': 'server_combined_tips'})"><i class="i_help"></i></a>
        </li>
			<div class="frm_btn"> 
			<input type="hidden" name="sid" value="<?php echo $data['info']['sid']; ?>">
			<input type="hidden" name="doSubmit" value="1">
			<input type="submit" id="btnsubmit" class="btn_sbm" value="<?php echo Lang('submit'); ?>">
			<input type="button" id="btnreset" class="btn_rst" value="<?php echo Lang('close'); ?>" onclick="$.dialog({id:'dialog_s_setting'}).close();">
			<input type="button" id="reset" class="btn_sbm" value="<?php echo Lang('reset');?>">
			</div>
        <li>
            <span class="frm_info">&nbsp;</span>
            <strong><?php echo Lang('server_attr_title'); ?></strong>
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('is_used'); ?>：</span>
			<label><input type="radio" id="is_use" name="is_use" value="0"<?php echo $data['info']['is_use'] == 0? ' checked': ''; ?>><?php echo Lang('nouse'); ?></label>
			<label><input type="radio" name="is_use" value="1" <?php echo $data['info']['is_use'] == 1? ' checked': ''; ?>><?php echo Lang('used'); ?></label> 
            <a href="javascript:void(0);" onmouseover="Ha.common.showItemTips('<?php echo Lang('server_is_use_tips'); ?>',this,{'id': 'is_use_tips'})"><i class="i_help"></i></a>
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('server_is_open'); ?>：</span>
			<label><input type="radio" name="open" value="0"<?php echo $data['info']['open'] == 0? ' checked': ''; ?>><?php echo Lang('close'); ?></label>
			<label><input type="radio" name="open" value="1" <?php echo $data['info']['open'] == 1? ' checked': ''; ?>><?php echo Lang('open'); ?></label> 
            <a href="javascript:void(0);" onmouseover="Ha.common.showItemTips('<?php echo Lang('server_is_open_tips'); ?>',this,{'id': 'is_open_tips'})"><i class="i_help"></i></a>
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('server_is_private'); ?>：</span>
			<label><input type="radio" name="private" value="0"<?php echo $data['info']['private'] == 0? ' checked': ''; ?>><?php echo Lang('private'); ?></label>
			<label><input type="radio" name="private" value="1" <?php echo $data['info']['private'] == 1? ' checked': ''; ?>><?php echo Lang('public'); ?></label> 
            <a href="javascript:void(0);" onmouseover="Ha.common.showItemTips('<?php echo Lang('server_is_private_tips'); ?>',this,{'id': 'is_private_tips'})"><i class="i_help"></i></a>
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('server_is_test'); ?>：</span>
			<label><input type="radio" name="test" value="1"<?php echo $data['info']['test'] == 1? ' checked': ''; ?>><?php echo Lang('test'); ?></label>
			<label><input type="radio" name="test" value="0" <?php echo $data['info']['test'] == 0? ' checked': ''; ?>><?php echo Lang('normal'); ?></label> 
            <a href="javascript:void(0);" onmouseover="Ha.common.showItemTips('<?php echo Lang('server_is_test_tips'); ?>',this,{'id': 'is_test_tips'})"><i class="i_help"></i></a>
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('server_test_player_num'); ?>：</span>
			<input type="text" name="test_player" id="test_player" class="ipt_txt_s" value="<?php echo $data['info']['test_player']; ?>"><?php echo Lang('server_test_player_num_item'); ?>
            <a href="javascript:void(0);" onmouseover="Ha.common.showItemTips('<?php echo Lang('server_test_player_num_tips'); ?>',this,{'id': 'player_num_tips'})"><i class="i_help"></i></a>
        </li>
        <li>
            <span class="frm_info">&nbsp;</span>
            <strong><?php echo Lang('server_active_setting'); ?></strong>
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('server_active_first_pay'); ?>：</span>
			<label><input type="radio" name="first_pay_act" value="0"<?php echo $data['info']['first_pay_act'] == 0? ' checked': ''; ?>><?php echo Lang('close'); ?></label>
			<label><input type="radio" name="first_pay_act" value="1" <?php echo $data['info']['first_pay_act'] == 1? ' checked': ''; ?>><?php echo Lang('open'); ?></label> 
            <a href="javascript:void(0);" onmouseover="Ha.common.showItemTips('<?php echo Lang('server_active_first_pay_tips'); ?>',this,{'id': 'first_pay_tips'})"><i class="i_help"></i></a>
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('server_active_level_order'); ?>：</span>
            <label><input type="radio" name="level_act" value="0"<?php echo $data['info']['level_act'] == 0? ' checked': ''; ?>><?php echo Lang('close'); ?></label>
            <label><input type="radio" name="level_act" value="1" <?php echo $data['info']['level_act'] == 1? ' checked': ''; ?>><?php echo Lang('open'); ?></label> 
            <a href="javascript:void(0);" onmouseover="Ha.common.showItemTips('<?php echo Lang('server_active_level_order_tips'); ?>',this,{'id': 'level_order_tips'})"><i class="i_help"></i></a>
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('server_active_mission_order'); ?>：</span>
			<label><input type="radio" name="mission_act" value="0"<?php echo $data['info']['mission_act'] == 0? ' checked': ''; ?>><?php echo Lang('close'); ?></label>
			<label><input type="radio" name="mission_act" value="1" <?php echo $data['info']['mission_act'] == 1? ' checked': ''; ?>><?php echo Lang('open'); ?></label> 
            <a href="javascript:void(0);" onmouseover="Ha.common.showItemTips('<?php echo Lang('server_active_mission_order_tips'); ?>',this,{'id': 'mission_order_tips'})"><i class="i_help"></i></a>
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('server_active_new_card'); ?>：</span>
			<label><input type="radio" name="new_card_act" value="0"<?php echo $data['info']['new_card_act'] == 0? ' checked': ''; ?>><?php echo Lang('close'); ?></label>
			<label><input type="radio" name="new_card_act" value="1" <?php echo $data['info']['new_card_act'] == 1? ' checked': ''; ?>><?php echo Lang('open'); ?></label> 
            <a href="javascript:void(0);" onmouseover="Ha.common.showItemTips('<?php echo Lang('server_active_new_card_tips'); ?>',this,{'id': 'new_card_tips'})"><i class="i_help"></i></a>
        </li>
        <li>
            <span class="frm_info">&nbsp;</span>
            <strong><?php echo Lang('server_api_port_pwd'); ?></strong>
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('server_api_address'); ?>：</span>
			<select name="api_server" id="api_server" onchange="$('#combined_server').val($(this).val() != '0' ? $(this).val(): '')" class="ipt_select">
				<option value="0"><?php echo Lang('no_setting'); ?></option>
				<?php echo $data['apistring'] ?>
			</select>
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('port'); ?>：</span>
			<input type="text" name="api_port" id="api_port" value="<?php echo $data['info']['api_port']; ?>" class="ipt_txt_s">
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('password'); ?>：</span>
			<input type="text" name="api_pwd" id="api_pwd" value="<?php echo $data['info']['api_pwd'] ? $data['info']['api_pwd'] : 'ybybyb'; ?>" class="ipt_txt">
        </li>
        <li>
            <span class="frm_info">&nbsp;</span>
            <strong><?php echo Lang('server_db_slave'); ?></strong>
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('server_db_address'); ?>：</span>
			<select name="db_server" id="db_server" class="ipt_select">
				<option value="0"><?php echo Lang('no_setting'); ?></option>
				<?php echo $data['dbstring'] ?>
			</select>
			<a href="javascript:;" class="test_db_connect"><?php echo Lang('server_db_test'); ?></a>
			<span id="connect_tips"></span>
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('server_db_name'); ?>：</span>
			<input type="text" name="db_name" id="db_name" value="<?php echo $data['info']['db_name'] ? $data['info']['db_name'] : 'gamedb_'.$data['slug']; ?>" class="ipt_txt">
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('server_db_root'); ?>：</span>
			<input type="text" name="db_root" id="db_root" value="<?php echo $data['info']['db_root']; ?>" class="ipt_txt">
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('password'); ?>：</span>
			<input type="text" name="db_pwd" id="db_pwd" value="<?php echo $data['info']['db_pwd']; ?>" class="ipt_txt">
        </li>
		<li>
			<span class="frm_info">&nbsp;</span>
		</li>	       
    </ul>
</div>

</form>