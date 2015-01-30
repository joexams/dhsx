<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">

$(function(){

	/**
	 * 提交
	 * @param  {[type]} e [description]
	 * @return {[type]}   [description]
	 */
	$('#post_submit').on('submit', function(e){
		e.preventDefault();
		var objform = $(this);
		$('#btn_sbm').attr('disabled', 'disabled');
		var url = '<?php echo INDEX; ?>?m=operation&c=notice&v=clear';
		Ha.common.ajax(url, 'json', objform.serialize(), 'post', 'ajax-submit-area', function(data){
			if (data.status == 0) {
				Ha.page.getList(Ha.page.pageIndex);
				$.dialog({id: 'notice_view'}).close();
			}
		});
		return false;
	});

});
</script>
<form id="post_submit" method="post" action="">
<div class="frm_cont" id="ajax-submit-area">
    <ul id="info_list">
    <?php foreach ($data['list'] as $key => $value){ ?>
    <li><input type="checkbox" name="sid[]" value="<?php echo $value['sid'];?>"><?php echo $value['name'];?></li>
    <?php } ?>
    </ul>
</div>
<div class="float_footer">    
	<div class="frm_btn"> 
    <input type="hidden" name="doSubmit" value="1">
	<input type="hidden" name="nid" id="nid" value="<?php echo $nid ?>">
    <input type="submit" id="btnsubmit" class="btn_sbm" value="<?php echo Lang('clear'); ?>">
	<input type="button" id="btnreset" class="btn_rst" value="<?php echo Lang('close'); ?>" onclick="$.dialog({id:'notice_view'}).close();">
	</div>
</div>
</form>