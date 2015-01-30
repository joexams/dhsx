<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function(){
	//-------添加
	$('#post_submit').on('submit', function(e){
		e.preventDefault();
		var objform = $(this);
		var url = '<?php echo INDEX; ?>?m=manage&c=template&v=add';
		Ha.common.ajax(url, 'json', objform.serialize(), 'post', 'ajax-submit-area', function(data){
			if (data.status == 0) {
				if (data.editflag == 1){
					Ha.page.getList( Ha.page.pageIndex );
				}else {
					$( "#tmpllisttpl" ).tmpl( data.info ).prependTo( "#tmpllist" ).fadeIn(2000, function(){
						var obj = $(this);
						obj.css('background', '#E6791C');
						setTimeout( function(){	obj.css('background', ''); }, ( 2000 ) );
					});	
				}
				$.dialog({id:'templateManageDialog'}).close();
			}
		});
	});

	$('#ajax-submit-area').on('click', 'a.addargs', function(){
		var htmlstr = $(this).parent('p').clone().html();
		htmlstr = htmlstr.replace(/\+/g, '-');
		htmlstr = htmlstr.replace(/addargs/g, 'subargs');
		$(this).parent('p').after('<p style="margin-left: 125px;">'+htmlstr+'</p>');
	});
	$('#ajax-submit-area').on('click', 'a.subargs', function(){
		$(this).parent('p').remove();
	});

	$('#ajax-submit-area').on('click', 'a.preview', function(){
		var strHtml = '<div class="frm_cont"><ul>'+$(this).siblings('textarea').text()+'</ul></div>';
		Ha.Dialog.show(strHtml, "<?php echo Lang('preview') ?>", 500, 'dialog_preview');
	});

});
</script>

<form name="post_submit" id="post_submit" method="post">
<div class="frm_cont" id="ajax-submit-area">
    <ul>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('template_form_title'); ?>：</span>
            <input type="text" name="title" id="title" class="ipt_txt" value="<?php echo $data['info']['title']; ?>">
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('template_form_version') ?>：</span>
			<select name="version" id="version" class="ipt_select">
				<option value=""><?php echo Lang('no_setting') ?></option>
				<?php foreach ($arr_version as $key => $value) { ?>
				<option value="<?php echo $value['name'] ?>"<?php echo $value['name'] == $data['info']['version'] ? 'selected' : '' ?>><?php echo $value['name'] ?></option>
				<?php } ?>
			</select>
			<span class="graytitle"><?php echo Lang('template_form_version_tips') ?></span>
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('template_form_key') ?>：</span>
			<input type="text" name="key" id="key" class="ipt_txt" value="<?php echo $data['info']['key']; ?>">
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('template_form_args')?>：</span>

			<?php if ($data['info']['args']) { ?>

			<?php foreach ($data['info']['args'] as $key => $value) { ?>
				<?php if ($key == 0) { ?>
				<p>
				<?php echo Lang('template_form_key_args_name') ?>  <input type="text" name="arg[]" value="<?php echo $value['arg'] ?>" class="ipt_txt_s" />
				<?php echo Lang('template_form_key_args_name_desc') ?>  <input type="text" name="arg_tips[]" value="<?php echo $value['tips'] ?>" class="ipt_txt_s" />
				<a href="javascript:;" class="addargs"><strong>+</strong></a>
				</p>
				<?php }else { ?>
				<p style="margin-left: 125px;">
				<?php echo Lang('template_form_key_args_name') ?>  <input type="text" name="arg[]" value="<?php echo $value['arg'] ?>"  class="ipt_txt_s"  />
				<?php echo Lang('template_form_key_args_name_desc') ?>  <input type="text" name="arg_tips[]" value="<?php echo $value['tips'] ?>"  class="ipt_txt_s"  />
				<a href="javascript:;" class="subargs"><strong>-</strong></a>
				</p>
				<?php } ?>
			<?php } ?>

			<?php }else { ?>
			<p>
			<?php echo Lang('template_form_key_args_name') ?>  <input type="text" name="arg[]" value="" class="ipt_txt_s" />
			<?php echo Lang('template_form_key_args_name_desc') ?>  <input type="text" name="arg_tips[]" value="" class="ipt_txt_s" />
			<a href="javascript:;" class="addargs"><strong>+</strong></a>
			</p>
			<?php } ?>
        </li>
        <li>
            <span class="frm_info"><em>*</em><?php echo Lang('template_form_key_rtns') ?>：</span>

            <?php if ($data['info']['rtns']) { ?>

			<?php foreach ($data['info']['rtns'] as $key => $value) { ?>
				<?php if ($key == 0) { ?>
				<p>
				<?php echo Lang('template_form_key_rtns_name') ?>  <input type="text" name="rtn[]" value="<?php echo $value['rtn'] ?>" class="ipt_txt_s"/>
				<?php echo Lang('template_form_key_rtns_name_desc') ?>  <input type="text" name="rtn_tips[]" value="<?php echo $value['tips'] ?>" class="ipt_txt_s"/>
				<a href="javascript:;" class="addargs"><strong>+</strong></a>
				</p>
				<?php }else { ?>
				<p style="margin-left: 125px;">
				<?php echo Lang('template_form_key_rtns_name') ?>  <input type="text" name="rtn[]" value="<?php echo $value['rtn'] ?>" class="ipt_txt_s"/>
				<?php echo Lang('template_form_key_rtns_name_desc') ?>  <input type="text" name="rtn_tips[]" value="<?php echo $value['tips'] ?>" class="ipt_txt_s"/>
				<a href="javascript:;" class="subargs"><strong>-</strong></a>
				</p>
				<?php } ?>
			<?php } ?>

			<?php }else { ?>
			<p>
			<?php echo Lang('template_form_key_rtns_name') ?>  <input type="text" name="rtn[]" value="" class="ipt_txt_s"/>
			<?php echo Lang('template_form_key_rtns_name_desc') ?>  <input type="text" name="rtn_tips[]" value="" class="ipt_txt_s"/>
			<a href="javascript:;" class="addargs"><strong>+</strong></a>
			</p>
			<?php } ?>
        </li>
        <li>
            <span class="frm_info"><?php echo Lang('template_form_content') ?>：</span>
            <textarea name="content" id="content" style="width:300px;height:100px;"><?php echo $data['info']['content']; ?></textarea>
	    </li>
    </ul>
</div>
<div class="float_footer">    
	<div class="frm_btn"> 
    <input type="hidden" name="doSubmit" value="1">
	<input type="hidden" name="tid" id="tid" value="<?php echo $data['info']['tid'] ?>">
    <input type="submit" id="btnsubmit" class="btn_sbm" value="<?php echo Lang('submit'); ?>">
	<input type="button" id="btnreset" class="btn_rst" value="<?php echo Lang('close'); ?>" onclick="$.dialog({id:'templateManageDialog'}).close();">
	</div>
</div>
</form>