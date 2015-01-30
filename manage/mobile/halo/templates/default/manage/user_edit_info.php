<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function(){
	$('#post_submit').on('submit', function(e) {
		e.preventDefault();
		var objform = $(this);
		var url = '<?php echo INDEX; ?>?m=manage&c=user&v=edit_password';
		Ha.common.ajax(url, 'json', objform.serialize(), 'post');
	});
});
</script>
<div id="tipsBar" class="msg_tips"> 
    <i class="i_tips"></i>
    <p id="p_tips"><?php echo Lang('edit_password_tips'); ?></p>
</div>
<h2><span id="tt"><?php echo Lang('edit_person_info'); ?></span></h2>
<div class="container" id="container">
	<form name="post_submit" id="post_submit" method="post">
	<div class="frm_cont">
	    <ul>
	        <li>
	            <span class="frm_info"><em>*</em><?php echo Lang('username'); ?>：</span>
	            <?php echo $data['info']['username']; ?>
	        </li>
	        <li>
	            <span class="frm_info"><em>*</em><?php echo Lang('realname'); ?>：</span>
	            <?php echo $data['info']['realname']; ?>
	        </li>
	        <li>
	            <span class="frm_info"><em>*</em><?php echo Lang('select_language') ?>：</span>
	    		<select name="lang" id="lang">
	          	    <option value="zh-cn"<?php echo $data['info']['lang'] == 'zh-cn' ? ' selected': '' ?>><?php echo Lang('default_select_language')?></option>
	          	    <option value="zh-tw"<?php echo $data['info']['lang'] == 'zh-tw' ? ' selected': '' ?>><?php echo Lang('traditional_chinese')?></option>
	          	    <option value="en"<?php echo $data['info']['lang'] == 'en' ? ' selected': '' ?>>English</option>
	        	</select>
			</li>
	        <li>
	            <span class="frm_info"><em>*</em><?php echo Lang('oldpassword') ?>：</span>
				<input type="password" name="oldpassword" id="oldpassword" class="ipt_txt">
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
	            <span class="frm_info">&nbsp;</span>
	            <input type="hidden" name="doSubmit" value="1">
	            <input type="hidden" name="userid" value="<?php echo $data['info']['userid'] ?>" />
	            <input type="submit" id="btnsubmit" class="btn_sbm" value="<?php echo Lang('submit'); ?>">
	        </li>  
	    </ul>
	</div>
	</form>
</div>
