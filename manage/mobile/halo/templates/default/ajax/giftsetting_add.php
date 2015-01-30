<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function(){
	$('#post_submit').on('submit', function(e) {
		e.preventDefault();
		var obj = $(this);
			var url = '<?php echo INDEX; ?>?m=operation&c=giftsetting&v=add';
			Ha.common.ajax(url, 'json', obj.serialize(), 'post', 'ajax-submit-area', function(data){
				
			});
	});
	$('#ajax-submit-area').on('click', 'a.sub', function() {
	  $(this).parent().remove();
	});
	$('#ajax-submit-area').on('click', 'a.add', function() {
	  var str = $(this).parent().clone().html();
	  str = str.replace(/\+/g, '-').replace(/add/g, 'sub');
	  $('<li>'+str+'</li>').insertAfter($(this).parent());
	});
});

</script>
<form name="post_submit" id="post_submit" method="post">
<div class="frm_cont" id="ajax-submit-area">
    <ul>
        <li>
            <span class="frm_info"><?php echo Lang('giftname'); ?>：</span>
            <input type="text" name="giftname" id="giftname" class="ipt_txt" value="<?php echo $data['info']['giftname']; ?>">
        </li>
        <li>
            <span class="frm_info"><?php echo Lang('gifttype') ?>：</span>
			<input type="radio" name="gifttype" value="0" <?php if ($data['info']['gifttype'] == 0){ ?> checked <?php } ?> id="gifttype0"><?php echo Lang('each_server_totle') ?>
			<input type="radio" name="gifttype" value="1" <?php if ($data['info']['gifttype'] == 1){ ?> checked <?php } ?> id="gifttype1"><?php echo Lang('each_server_everyday_number') ?>
        </li>
        <li>
            <span class="frm_info"><?php echo Lang('number_of_gift') ?>：</span>
            <input type="text" name="limitnumber" id="limitnumber" class="ipt_txt" value="<?php echo $data['info']['limitnumber']; ?>">
        </li>
        <li>
            <span class="frm_info"><?php echo Lang('gift_time_limit') ?>：</span>
            <input type="text" name="starttime" id="starttime" onclick="WdatePicker()" readonly class="ipt_txt" value="<?php echo $data['info']['starttime']; ?>">
            -
            <input type="text" name="endtime" id="endtime" onclick="WdatePicker()" readonly class="ipt_txt" value="<?php echo $data['info']['endtime']; ?>">
        </li>
        <li>
            <span class="frm_info"><?php echo Lang('gift_message_info') ?>：</span>
            <input type="text" name="message" id="message" class="ipt_txt" value="<?php echo $data['info']['message']; ?>">
        </li>
        <li>
            <span class="frm_info"><?php echo Lang('coin_number') ?>：</span>
            <input type="text" name="coins" id="coins" class="ipt_txt" value="<?php echo $data['info']['coin']; ?>">
        </li>
        <li>
            <span class="frm_info"><?php echo Lang('skill_number') ?>：</span>
            <input type="text" name="skill" id="skill" class="ipt_txt" value="<?php echo $data['info']['skill']; ?>">
        </li>
        <li>
            <span class="frm_info"><?php echo Lang('fame_number') ?>：</span>
            <input type="text" name="fame" id="fame" class="ipt_txt" value="<?php echo $data['info']['fame']; ?>">
        </li>
        <li>
            <span class="frm_info"><?php echo Lang('ingot_number') ?>：</span>
            <input type="text" name="ingot" id="ingot" class="ipt_txt" value="<?php echo $data['info']['ingot']; ?>">
        </li>
        <?php 
        	if (count($data['info']['itemlist'])>0){
        		foreach ($data['info']['itemlist'] as $key => $value){
        ?>
        <li>
            <span class="frm_info"><?php echo Lang('prop_item') ?>：</span>
			<?php echo Lang('item') ?>I  D：<input type="text" name="item[id][]" value="<?php echo $value['item_id'] ?>" size="10"> &nbsp;&nbsp;
			<?php echo Lang('item_level') ?>：<input type="text" name="item[level][]" value="<?php echo $value['level'] ?>" size="10"> &nbsp;&nbsp;
			<?php echo Lang('item_number') ?>：<input type="text" name="item[number][]" value="<?php echo $value['number'] ?>" size="10"> &nbsp;&nbsp;
			<?php if ($key==0){ ?>
			<a href="javascript:;" style="font-size:14px;font-weight:600" class="add">+</a>
			<?php }else{?>
			<a href="javascript:;" style="font-size:14px;font-weight:600" class="sub">-</a>
			<?php }?>
        </li>
        <?php
        		}
        	}else{
        ?>
        <li>
            <span class="frm_info"><?php echo Lang('prop_item') ?>：</span>
			<?php echo Lang('item') ?>I  D：<input type="text" name="item[id][]" value="" size="10"> &nbsp;&nbsp;
			<?php echo Lang('item_level') ?>：<input type="text" name="item[level][]" value="1" size="10"> &nbsp;&nbsp;
			<?php echo Lang('item_number') ?>：<input type="text" name="item[number][]" value="1" size="10"> &nbsp;&nbsp;
			<a href="javascript:;" style="font-size:14px;font-weight:600" class="add">+</a>
        </li>
        <?php }?>
        <li style="border-style: none; border-top-style: dashed;border-width: 1px;"></li>
        <?php 
        	if (count($data['info']['fatelist'])>0){
        		foreach ($data['info']['fatelist'] as $key => $value){
        ?>
        <li>
            <span class="frm_info"><?php echo Lang('fate') ?>：</span>
            <?php echo Lang('fate') ?>I  D：<input type="text" name="fate[id][]" value="<?php echo $value['fate_id'] ?>" size="10"> &nbsp;&nbsp;
			<?php echo Lang('fate_level') ?>：<input type="text" name="fate[level][]" value="<?php echo $value['level'] ?>" size="10"> &nbsp;&nbsp;
			<?php echo Lang('fate_number') ?>：<input type="text" name="fate[number][]" value="<?php echo $value['number'] ?>" size="10"> &nbsp;&nbsp;
			<?php if ($key==0){ ?>
			<a href="javascript:;" style="font-size:14px;font-weight:600" class="add">+</a>
			<?php }else{?>
			<a href="javascript:;" style="font-size:14px;font-weight:600" class="sub">-</a>
			<?php }?>
        </li>
        <?php
        		}
        	}else{
        ?>
        <li>
            <span class="frm_info"><?php echo Lang('fate') ?>：</span>
			<?php echo Lang('fate') ?>I  D：<input type="text" name="fate[id][]" value="" size="10"> &nbsp;&nbsp;
			<?php echo Lang('fate_level') ?>：<input type="text" name="fate[level][]" value="1" size="10"> &nbsp;&nbsp;
			<?php echo Lang('fate_number') ?>：<input type="text" name="fate[number][]" value="1" size="10"> &nbsp;&nbsp;
			<a href="javascript:;" style="font-size:14px;font-weight:600" class="add">+</a>
        </li>
        <?php }?>
        <li style="border-style: none; border-top-style: dashed;border-width: 1px;"></li>
        <?php 
        	if (count($data['info']['soullist'])>0){
        		foreach ($data['info']['soullist'] as $key => $value){
        ?>
        <li>
            <span class="frm_info"><?php echo Lang('soul') ?>：</span>
            <?php echo Lang('soul') ?>I  D：<input type="text" name="soul[id][]" value="<?php echo $value['soul_id'] ?>" size="10"> &nbsp;&nbsp;
			<?php echo Lang('soul_number') ?>：<input type="text" name="soul[number][]" value="<?php echo $value['number'] ?>" size="10"> &nbsp;&nbsp;
			<?php if ($key==0){ ?>
			<a href="javascript:;" style="font-size:14px;font-weight:600" class="add">+</a>
			<?php }else{?>
			<a href="javascript:;" style="font-size:14px;font-weight:600" class="sub">-</a>
			<?php }?>
        </li>
        <?php
        		}
        	}else{
        ?>
        <li>
            <span class="frm_info"><?php echo Lang('soul') ?>：</span>
            <?php echo Lang('soul') ?>I  D：<input type="text" name="soul[id][]" value="" size="10"> &nbsp;&nbsp;
			<?php echo Lang('soul_number') ?>：<input type="text" name="soul[number][]" value="1" size="10"> &nbsp;&nbsp;
			<a href="javascript:;" style="font-size:14px;font-weight:600" class="add">+</a>
        </li>
         <?php }?>  
    </ul>
</div>
<div class="float_footer">
	<div class="frm_btn"> 
	<input type="hidden" name="doSubmit" value="1">
	<input type="hidden" name="giftid" value="<?php echo $giftid;?>">
	<input type="submit" id="btnsubmit" class="btn_sbm" value="<?php echo Lang('submit'); ?>">
	<input type="button" id="btnreset" class="btn_rst" value="<?php echo Lang('close'); ?>" onclick="$.dialog({id:'itemApplyManageDialog'}).close();">
	</div>
</div>
</form>