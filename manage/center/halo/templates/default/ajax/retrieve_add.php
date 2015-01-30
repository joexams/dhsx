<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function(){
    $($('#chk-area').html()).appendTo('#ajax-submit-area');
    $('#post_submit').on('submit', function(e){
        e.preventDefault();
        var obj = $(this);
        var url = '<?php echo INDEX; ?>?m=operation&c=interactive&v=add_retrieve';
        Ha.common.ajax(url, 'json', obj.serialize(), 'post', 'ajax-submit-area', function(data){
            if (data.status == 0) {
                $.dialog({id:'rebackDlg'}).close();
            }
        });
    });
});
</script>
<form name="post_submit" id="post_submit" method="post">
<div class="frm_cont" id="ajax-submit-area">
    <ul>
        <li>
            <span class="frm_info">&nbsp;</span>
            <label><?php echo Lang('selected'); ?><strong class="orangetitle"><?php echo intval($_GET['num']) ?></strong><?php echo Lang('row').Lang('log'); ?></label>
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('stolen_find_desc'); ?>：</span>
            <textarea name="content" style="width:350px;height:80px;"></textarea>
        </li>
    </ul>
</div>
<div class="float_footer">    
	<div class="frm_btn"> 
    <input type="hidden" name="doSubmit" value="1" />
    <input type="hidden" name="sid" value="<?php echo intval($_GET['sid']); ?>" />
    <input type="hidden" name="id" value="<?php echo intval($_GET['id']); ?>" />
    <input type="hidden" name="playername" value="<?php echo urldecode($_GET['playername']); ?>">
    <input type="hidden" name="nickname" value="<?php echo urldecode($_GET['nickname']); ?>">
    <input type="submit" id="btnsubmit" class="btn_sbm" value="<?php echo Lang('submit'); ?>">
	<input type="button" id="btnreset" class="btn_rst" value="<?php echo Lang('close'); ?>" onclick="$.dialog({id:'rebackDlg'}).close();">
	</div>
</div>
</form>