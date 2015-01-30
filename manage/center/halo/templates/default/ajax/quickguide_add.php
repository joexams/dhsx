<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function(){
	//--------添加
	$('#post_submit').on('submit', function(e){
		e.preventDefault();
		var objform = $(this);
		var url = '<?php echo INDEX; ?>?m=manage&c=quickguide&v=public_setting';
		Ha.common.ajax(url, 'json', objform.serialize(), 'post', 'ajax-submit-area', function(data){
			if (data.status == 0) {
				$('#quickguidetpl').tmpl(data.info).prependTo('#quickguidelist');
				$.dialog({id:'quickguideDlg'}).close();
			}
		});
	});
});
</script>

<form name="post_submit" id="post_submit" method="post">
<div class="frm_cont" id="ajax-submit-area">
    <ul>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('now_url')?>：</span>
            <input type="text" name="qurl" id="qurl" class="ipt_txt_l" value="<?php echo $_GET['qurl']; ?>" readonly>
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('shortcut_name')?>：</span>
            <input type="text" name="qname" id="qname" class="ipt_txt_l" value="<?php echo $_GET['qname']; ?>">
        </li>
    </ul>
</div>
<div class="float_footer">    
	<div class="frm_btn"> 
    <input type="hidden" name="doSubmit" value="1">
    <input type="submit" id="btnsubmit" class="btn_sbm" value="<?php echo Lang('submit'); ?>">
	<input type="button" id="btnreset" class="btn_rst" value="<?php echo Lang('close'); ?>" onclick="$.dialog({id:'quickguideDlg'}).close();">
	</div>
</div>
</form>