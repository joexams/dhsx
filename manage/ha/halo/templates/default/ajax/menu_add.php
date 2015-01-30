<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function(){
	//--------添加
	$('#post_submit').on('submit', function(e){
		e.preventDefault();
		var objform = $(this);
		var url = '<?php echo INDEX; ?>?m=manage&c=menu&v=add';
		Ha.common.ajax(url, 'json', objform.serialize(), 'post', 'ajax-submit-area', function(data){
			if (data.status == 0) {
				$.dialog({id:'menuManageDialog'}).close();
			}
		});
	});
});
</script>

<form name="post_submit" id="post_submit" method="post">
<div class="frm_cont" id="ajax-submit-area">
    <ul>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('menu_name'); ?>：</span>
            <input type="text" name="mname" id="mname" class="ipt_txt" value="<?php echo $data['info']['mname']; ?>">
        </li>
        <li>
            <span class="frm_info"><em>*</em>默认一级节点：</span>
            <select name="parentid" id="parentid" class="ipt_select">
				<option value="0" selected><?php echo Lang('default_parent'); ?></option>
				<?php echo $tree_str ?>
			</select>
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('is_link') ?>：</span>
			<label><input type="radio" name="islink" value="0" <?php if ($data['info']['islink'] == 0) { ?>checked="checked"<?php } ?>><?php echo Lang('no'); ?></label>
			<label><input type="radio" name="islink" value="1" <?php if ($data['info']['islink'] == 1) { ?>checked="checked"<?php } ?>><?php echo Lang('yes'); ?></label>
        </li>
        <li>
            <span class="frm_info"><?php echo Lang('link_url'); ?>：</span>
            <input type="text" name="urllink" id="urllink" class="ipt_txt" value="<?php echo $data['info']['urllink']; ?>">
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('is_distrib') ?>：</span>
			<label><input type="radio" name="isdistrib" value="0" <?php if ($data['info']['isdistrib'] == 0) { ?>checked="checked"<?php } ?>><?php echo Lang('can'); ?></label>
			<label><input type="radio" name="isdistrib" value="1" <?php if ($data['info']['isdistrib'] == 1) { ?>checked="checked"<?php } ?>><?php echo Lang('canot'); ?></label>
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('modules'); ?>：</span>
            <input type="text" name="m" id="modules" class="ipt_txt" value="<?php echo $data['info']['m']; ?>">
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('controls'); ?>：</span>
            <input type="text" name="c" id="controls" class="ipt_txt" value="<?php echo $data['info']['c']; ?>">
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('views'); ?>：</span>
            <input type="text" name="v" id="views" class="ipt_txt" value="<?php echo $data['info']['v']; ?>">
        </li>
        <li>
            <span class="frm_info"><?php echo Lang('guidedata'); ?>：</span>
            <input type="text" name="data" id="guidedata" class="ipt_txt" value="<?php echo $data['info']['data']; ?>">
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('displaystatus') ?>：</span>
			<label><input type="radio" name="display" value="0" <?php if ($data['info']['display'] == 0) { ?>checked="checked"<?php } ?>><?php echo Lang('displaynone'); ?></label>
			<label><input type="radio" name="display" value="1" <?php if ($data['info']['display'] == 1) { ?>checked="checked"<?php } ?>><?php echo Lang('display'); ?></label>
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
	<input type="hidden" name="mid" id="mid" value="<?php echo $data['info']['mid'] ?>">
    <input type="submit" id="btnsubmit" class="btn_sbm" value="<?php echo Lang('submit'); ?>">
	<input type="button" id="btnreset" class="btn_rst" value="<?php echo Lang('close'); ?>" onclick="$.dialog({id:'menuManageDialog'}).close();">
	</div>
</div>
</form>