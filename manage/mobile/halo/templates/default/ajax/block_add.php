<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function(){
	//--------添加
	$('#post_submit').on('submit', function(e){
		e.preventDefault();
		var objform = $(this);
		var url = '<?php echo INDEX; ?>?m=manage&c=block&v=add';
		Ha.common.ajax(url, 'json', objform.serialize(), 'post', 'ajax-submit-area', function(data){
			if (data.status == 0) {
				if (data.editflag == 1){

				}else {
					zNodes.push(data.info);
					$.fn.zTree.init($("#blocktree"), setting, zNodes);
				}
				$.dialog({id:'blockManageDialog'}).close();
			}
		});
	});

});
</script>
<form name="post_submit" id="post_submit" method="post">
<div class="frm_cont" id="ajax-submit-area">
    <ul>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('block_name'); ?>：</span>
            <input type="text" name="bname" id="bname" class="ipt_txt" value="<?php echo $data['info']['bname']; ?>">
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('parent_block_name') ?>：</span>
            <select name="parentid" id="parentid">
				<option value="0"><?php echo Lang('default_parent'); ?></option>
				<?php echo $tree_str ?>
			</select>
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('block_key'); ?>：</span>
            <input type="text" name="key" id="key" class="ipt_txt" value="<?php echo $data['info']['key']; ?>">
        </li>
        <li>
            <span class="frm_info"><?php echo Lang('version'); ?>：</span>
            <select name="version" id="version" class="ipt_select">
            	<option value=""><?php echo Lang('no_setting') ?></option>
				<?php foreach ($arr_version as $key => $value) { ?>
				<option value="<?php echo $value['name'] ?>"<?php echo $value['name'] == $data['info']['version'] ? 'selected' : '' ?>><?php echo $value['name'] ?></option>
				<?php } ?>
            </select>
        </li>
        <li>
            <span class="frm_info"><?php echo Lang('listsort'); ?>：</span>
            <input type="text" name="listorder" id="listorder" class="ipt_txt_s" value="<?php echo $data['info']['listorder']; ?>">
        </li>
    </ul>
</div>
<div class="float_footer">    
	<div class="frm_btn"> 
    <input type="hidden" name="doSubmit" value="1">
	<input type="hidden" name="bid" id="bid" value="<?php echo $data['info']['bid'] ?>">
    <input type="submit" id="btnsubmit" class="btn_sbm" value="<?php echo Lang('submit'); ?>">
	<input type="button" id="btnreset" class="btn_rst" value="<?php echo Lang('close'); ?>" onclick="$.dialog({id:'blockManageDialog'}).close();">
	</div>
</div>
</form>