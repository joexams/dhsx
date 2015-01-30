<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function(){
    $('#platformitemtpl').tmpl(global_companylist).appendTo('#platformitem');
    //添加 
    $('#post_submit').on('submit', function(e){
        e.preventDefault();
        var objform = $(this);
        var url = '<?php echo INDEX; ?>?m=manage&c=priv&v=platform_group_add';
        Ha.common.ajax(url, 'json', objform.serialize(), 'post', 'container', function(data){
            if (data.status == 0) {
            	if (data.editflag != 1 ){
                $( "#platformlisttpl" ).tmpl( data.info ).prependTo( "#platformlist" ).fadeIn(2000, function(){
                    var obj = $(this);
                    obj.css('background', '#E6791C');
                    setTimeout( function(){ obj.css('background', ''); }, ( 2000 ) );
                }); 
            	}
                $.dialog({id:'platformManageDialog'}).close();
            }
        });
    });
});
</script>
<script type="text/template" id="platformitemtpl">
    <label><input type="checkbox" name="cid[]" value="${cid}" <?php if ($platform['cids']){ foreach (explode(",",trim($platform['cids'],",")) as $cvalue){?> {{if (cid == <?php echo $cvalue?>) }} <?php echo 'checked';?>{{/if}}<?php }}?>>${name}</label>
</script>
<form name="post_submit" id="post_submit" method="post">
<div class="frm_cont" id="ajax-submit-area">
    <ul>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('group_name') ?></span>
            <input type="text" name="gname" id="gname" class="ipt_txt" value="<?php echo $platform['gname'] ?>">
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('allow_platform') ?></span>
            <div id="platformitem">

            </div>
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('group_description') ?></span>
            <textarea name="description" id="description" style="width:300px;height:50px"><?php echo $platform['description'] ?></textarea>
        </li>
    </ul>
</div>
<div class="float_footer">    
	<div class="frm_btn"> 
    <input type="hidden" name="doSubmit" value="1">
    <input type="hidden" name="gid" id="gid" value="<?php echo $platform['gid']; ?>">
    <input type="submit" id="btnsubmit" class="btn_sbm" value="<?php echo Lang('submit'); ?>">
	<input type="button" id="btnreset" class="btn_rst" value="<?php echo Lang('close'); ?>" onclick="$.dialog({id:'platformManageDialog'}).close();">
	</div>
</div>
</form>