<script type="text/javascript">
$(function(){
	//--------添加
	$('#post_submit').on('submit', function(e){
		e.preventDefault();
		var objform = $(this);
		var url = '<?php echo INDEX; ?>?m=manage&c=role&v=add';
		Ha.common.ajax(url, 'json', objform.serialize(), 'post', 'container', function(data){
			if (data.status == 0) {
				if (data.editflag == 1){
					Ha.page.getList( Ha.page.pageIndex );
				}else {
					$( "#rolelisttpl" ).tmpl( data.info ).prependTo( "#rolelist" ).fadeIn(2000, function(){
						var obj = $(this);
						obj.css('background', '#E6791C');
						setTimeout( function(){	obj.css('background', ''); }, ( 2000 ) );
					});	
				}
				$.dialog({id:'roleManageDialog'}).close();
			}
		});
	});
});
</script>
<form name="post_submit" id="post_submit" method="post">
<div class="frm_cont">
    <ul>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('rolename'); ?>：</span>
            <input type="text" name="rolename" id="rolename" class="ipt_txt" value="<?php echo $data['info']['rolename']; ?>">
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('disabledstatus') ?>：</span>
			<input type="radio" name="disabled" value="0" <?php if ($data['info']['disabled'] == 0) { ?>checked="checked"<?php } ?>><span class="greentitle"><?php echo Lang('abled'); ?><?php echo Lang('is_abled')?></span>
			<input type="radio" name="disabled" value="1" <?php if ($data['info']['disabled'] == 1) { ?>checked="checked"<?php } ?>><?php echo Lang('disabled'); ?>
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('description') ?>：</span>
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
		<input type="hidden" name="roleid" id="roleid" value="<?php echo $data['info']['roleid'] ?>">
        <input type="submit" id="btnsubmit" class="btn_sbm" value="<?php echo Lang('submit'); ?>">
		<input type="button" id="btnreset" class="btn_rst" value="<?php echo Lang('close'); ?>" onclick="$.dialog({id:'roleManageDialog'}).close();">
   </div>
</div>
</form>