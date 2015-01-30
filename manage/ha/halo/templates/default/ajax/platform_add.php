<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function(){
	//添加 
	$('#post_submit').on('submit', function(e){
		e.preventDefault();
		var objform = $(this);
		var url = '<?php echo INDEX; ?>?m=manage&c=priv&v=platform_group';
		Ha.common.ajax(url, 'json', objform.serialize(), 'post', 'container', function(data){
			if (data.status == 0) {
				if (data.editflag == 1){
					Ha.page.getList( Ha.page.pageIndex );
				}else {
					$( "#platformlisttpl" ).tmpl( data.info ).prependTo( "#platformlist" ).fadeIn(2000, function(){
						var obj = $(this);
						obj.css('background', '#E6791C');
						setTimeout( function(){	obj.css('background', ''); }, ( 2000 ) );
					});	
				}
			}
		});
		return false;
	});

	if (typeof global_companylist != 'undefined') {
		$('#platformitemtpl').tmpl(global_companylist).appendTo('#platformitem');
	}
});
</script>

<form name="post_submit" id="post_submit" method="post">
<div class="frm_cont">
    <ul>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('group_name'); ?>：</span>
            <input type="text" name="gname" id="gname" class="ipt_txt" value="<?php echo $data['info']['gname']; ?>">
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('allow_platform') ?>：</span>
			<input type="checkbox" name="checkall" id="checkall" value="1"><?php echo Lang('all') ?>
			<p id="platformitem"> </p>
		</li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('group_description') ?>：</span>
            <input type="text" name="description" id="description" class="ipt_txt" value="<?php echo $data['info']['description']; ?>">
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('listsort') ?>：</span>
			<input type="text" name="listorder" id="listorder" class="ipt_txt_s">
        </li>       
    </ul>
</div>
<div class="float_footer">    
	<div class="frm_btn"> 
        <input type="hidden" name="doSubmit" value="1">
		<input type="hidden" name="gid" id="gid" value="<?php echo $data['info']['gid'] ?>">
        <input type="submit" id="btnsubmit" class="btn_sbm" value="<?php echo Lang('submit'); ?>">
		<input type="button" id="btnreset" class="btn_rst" value="<?php echo Lang('close'); ?>" onclick="$.dialog({id:'roleManageDialog'}).close();">
    </div>
</div>
</form>