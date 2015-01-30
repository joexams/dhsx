<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function(){
	$('#post_pop_submit').on('submit', function(e){
		e.preventDefault();
		var objform = $(this);
		var url = '<?php echo INDEX; ?>?m=develop&c=company&v=setting';
		Ha.common.ajax(url, 'json', objform.serialize(), 'post', 'container');
	});
});
</script>

<form name="post_pop_submit" id="post_pop_submit" method="post">
<div class="frm_cont">
    <ul>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('company_platform_type'); ?>：</span>
            <select name="type" id="type" class="ipt_select">
				<option value="1"<?php echo $data['info']['type'] == 1 ? ' selected' : '' ?>>1<?php echo Lang('company_platform_type_item') ?></option>
				<option value="2"<?php echo $data['info']['type'] == 2 ? ' selected' : '' ?>>2<?php echo Lang('company_platform_type_item') ?></option>
				<option value="3"<?php echo $data['info']['type'] == 3 ? ' selected' : '' ?>>3<?php echo Lang('company_platform_type_item') ?></option>
			</select>
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('company_name'); ?>：</span>
            <input type="text" name="name" id="name" value="<?php echo $data['info']['name']; ?>" class="ipt_txt">
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('company_short_name'); ?>：</span>
            <input type="text" name="slug" id="slug" value="<?php echo $data['info']['slug']; ?>" class="ipt_txt">
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('company_domain'); ?>：</span>
            <input type="text" name="web" id="web" value="<?php echo $data['info']['web']; ?>" class="ipt_txt">
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('company_game_title'); ?>：</span>
            <input type="text" name="game_name" id="game_name" value="<?php echo $data['info']['game_name']; ?>" class="ipt_txt">
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('company_pay_changer'); ?>：</span>
            <select name="money_type" id="money_type" class="ipt_select">
				<option value="0"><?php echo Lang('no_setting') ?></option>
				<?php echo $data['money_type_select'] ?>
			</select>
			<span class="graytitle"><?php echo Lang('company_pay_changer_tips'); ?></span>
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('company_pay_coins_rate'); ?>：</span>
            <input type="text" name="coins_rate" id="coins_rate" value="<?php echo $data['info']['coins_rate']; ?>" class="ipt_txt">
			<span class="graytitle"><?php echo Lang('company_pay_coins_rate_tips'); ?></span>
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('company_test_num_setting'); ?>：</span>
            <input type="text" name="t_player" id="t_player" value="<?php echo $data['info']['t_player']; ?>" class="ipt_txt_s">
			<span class="graytitle"><?php echo Lang('person'); ?></span>
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('company_communications_key'); ?>：</span>
            <input type="text" name="key" id="key" value="<?php echo $data['info']['key']; ?>" class="ipt_txt">
			<span class="graytitle"><?php echo Lang('company_communications_key_tips'); ?></span>
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('company_allow_pay_ip'); ?>：</span>
            <textarea name="charge_ips" id="charge_ips" style="width: 280px;height: 60px;"><?php echo $data['info']['charge_ips']; ?></textarea>
			<span class="graytitle"><?php echo Lang('company_allow_pay_ip_tips'); ?></span>
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('company_game_description'); ?>：</span>
             <input type="text" name="game_text" id="game_text" value="<?php echo $data['info']['game_text']; ?>" class="ipt_txt_l">
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('company_website'); ?>：</span>
             <input type="text" name="link" id="link" value="<?php echo $data['info']['link'][0]; ?>" class="ipt_txt_l">
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('company_bbs_url'); ?>：</span>
             <input type="text" name="link" id="bbs_url" value="<?php echo $data['info']['link'][1]; ?>" class="ipt_txt_l">
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('company_pay_url'); ?>：</span>
             <input type="text" name="link" id="pay_url" value="<?php echo $data['info']['link'][2]; ?>" class="ipt_txt_l">
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('company_pay_url'); ?>：</span>
             <input type="text" name="link" id="vip_url" value="<?php echo $data['info']['link'][3]; ?>" class="ipt_txt_l">
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('company_gm_url'); ?>：</span>
             <input type="text" name="link" id="gm_url" value="<?php echo $data['info']['link'][4]; ?>" class="ipt_txt_l">
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('company_fav_title'); ?>：</span>
             <input type="text" name="link" id="fav_title" value="<?php echo $data['info']['link'][5]; ?>" class="ipt_txt_l">
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('company_fav_url'); ?>：</span>
             <input type="text" name="link" id="fav_url" value="<?php echo $data['info']['link'][6]; ?>" class="ipt_txt_l">
        </li>
        <li>
            <span class="frm_info"><?php echo Lang('company_fcm_url'); ?>：</span>
             <input type="text" name="link" id="fcm_url" value="<?php echo $data['info']['link'][7]; ?>" class="ipt_txt_l">
             <span class="graytitle"><?php echo Lang('company_fcm_url_tips') ?></span>
        </li> 
		<li>
			<span class="graytitle"><?php echo Lang('company_url_args') ?></span>
			<span class="graytitle"><?php echo Lang('company_url_args_tips') ?></span>
		</li>	
        <li>
            <span class="frm_info"><?php echo Lang('language'); ?>：</span>
             <input type="text" name="locale" id="locale" value="<?php echo $data['info']['locale'] ?>" class="ipt_txt">
             <span class="graytitle"><?php echo Lang('timeoffset') ?></span>
        </li> 
		<li>
			<span class="frm_info">&nbsp;</span>
		</li>	       
    </ul>
</div>
<div class="float_footer">    
	<div class="frm_btn"> 
	<input type="hidden" name="cid" value="<?php echo $data['info']['cid']; ?>">
	<input type="hidden" name="doSubmit" value="1">
	<input type="submit" id="btnsubmit" class="btn_sbm" value="<?php echo Lang('submit'); ?>">
	<input type="button" id="btnreset" class="btn_rst" value="<?php echo Lang('close'); ?>" onclick="$.dialog({id:'dialog_c_setting_<?php echo $data['info']['cid']; ?>'}).close();">
	</div>
</div>
</form>