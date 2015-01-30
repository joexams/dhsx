<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function(){
	//--------添加
	$('#post_submit').on('submit', function(e){
		e.preventDefault();
		var objform = $(this);
		var url = '<?php echo INDEX; ?>?m=manage&c=user&v=add';
		if ($('#username').val().length <= 3) {
			Ha.notify.show('<?php echo Lang('username_length_error')?>');
			return ;
		}
		if ($('#realname').val().length <= 1) {
			Ha.notify.show('<?php echo Lang('realname_length_error')?>');
			return ;
		}
		<?php if (!isset($data['info']['userid']) || empty($data['info']['userid'])) { ?>
		if ($('#password').val().length <= 5) {
			Ha.notify.show('<?php echo Lang('please_enter_6plus_password')?>');
			return ;
		}
		<?php } ?>
		Ha.common.ajax(url, 'json', objform.serialize(), 'post', 'container', function(data){
			if (data.status == 0) {
				if (data.editflag == 1){
					Ha.page.getList(1);
				}else {
					$( "#userlisttpl" ).tmpl( data.info ).prependTo( "#userlist" ).fadeIn(2000, function(){
						var obj = $(this);
						obj.css('background', '#E6791C');
						setTimeout( function(){	obj.css('background', ''); }, ( 2000 ) );
					});	
				}
				$.dialog({id: 'authManageDialog'}).close();
			}
		});
		return false;
	});
});
</script>
<form name="post_submit" id="post_submit" method="post">
<div class="frm_cont">
    <ul>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('username'); ?>：</span>
            <input type="text" name="username" id="username" class="ipt_txt" value="<?php echo $data['info']['username']; ?>">
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('realname'); ?>：</span>
            <input type="text" name="realname" id="realname" class="ipt_txt" value="<?php echo $data['info']['realname']; ?>">
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('status') ?>：</span>
			<input type="radio" name="status" value="0" <?php if ($data['info']['status'] == 0) { ?>checked="checked"<?php } ?>><?php echo Lang('use_status_normal')?>
			<input type="radio" name="status" value="1" <?php if ($data['info']['status'] == 1) { ?>checked="checked"<?php } ?>><?php echo Lang('disable_player_login')?>
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('password') ?>：</span>
			<input type="password" name="password" id="password" class="ipt_txt">
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('re_password') ?>：</span>
			<input type="password" name="repassword" id="repassword" class="ipt_txt">
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('select_role') ?>：</span>
			<select name="roleid" id="roleid" class="ipt_select">
				<?php foreach ($data['rolelist'] as $key => $value) { ?>
					<option value="<?php echo $value['roleid']; ?>" <?php if ($data['info']['roleid'] == $value['roleid']){ ?>selected="selected"<?php } ?>><?php echo $value['rolename']; ?></option>
				<?php } ?>
	    	</select>
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('select_language') ?>：</span>
			<select name="lang" id="lang" class="ipt_select">
	      	    <option value="zh-cn" <?php if ($data['info']['lang'] == 'zh-cn'){ ?>selected="selected"<?php } ?>><?php echo Lang('default_select_language')?></option>
	      	    <option value="zh-tw" <?php if ($data['info']['lang'] == 'zh-tw'){ ?>selected="selected"<?php } ?>><?php echo Lang('traditional_chinese')?></option>
	      	    <option value="en" <?php if ($data['info']['lang'] == 'en'){ ?>selected="selected"<?php } ?>>English</option>
	    	</select>
        </li>
       <li>
            <span class="frm_info">&nbsp;</span>
           </li>	        
    </ul>
</div>
<div class="float_footer">    
	<div class="frm_btn"> 
	<input type="hidden" name="doSubmit" value="1">
	<input type="hidden" name="userid" id="userid" value="<?php echo $data['info']['userid'] ?>">
	<input type="submit" id="btnsubmit" class="btn_sbm" value="<?php echo Lang('submit'); ?>">
	<input type="button" id="btnreset" class="btn_rst" value="<?php echo Lang('close'); ?>" onclick="$.dialog({id:'authManageDialog'}).close();">
	</div>
</div>
</form>