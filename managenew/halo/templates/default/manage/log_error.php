<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var pageIndex = 1, pageCount = 0, pageSize = 15, recordNum = 0, op = 'error', month = '', loglist;
function getList(index){
	var query = "<?php echo INDEX; ?>?m=manage&c=log&v=ajax_list&op=error&top="+index+"&recordnum="+recordNum+'&day='+month;
	pageIndex = index;
	$( "#loglist" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			success: showList
		});
	});
}
function showList( data) {
	if (data.status == -1){
		$('#loglist').html(data.msg);
	}else {
		recordNum = data.count;
		pageCount = Math.ceil( data.count/pageSize ), loglist = data.list;

		$( ".pager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getList });
		$( "#loglist" ).empty();
		if (data.count > 0){
			$( "#loglisttpl" ).tmpl( loglist ).prependTo( "#loglist" );
			$( "#loglist" ).stop(true,true).hide().slideDown(400);
			if (pageCount > 1){
				$( "#loglist" ).parent().parent('div.content').css('height', $('#loglist').parent('table.global').css('height'));
			}
		}
	}
}

var dialog = dialog != undefined ? null : '';
$(document).ready(function(){
	getList( pageIndex );

	//---------删除
	$('#loglist').on('click', 'a.delete', function(){
		var obj    = $(this);
		var logid = obj.attr('data-logid');
		if (logid > 0){
			$.ajax({
				url: '<?php echo INDEX; ?>?m=manage&c=log&v=delete',
				data: 'logid='+logid+'&op='+op,
				dataType: 'json',
				type: 'post',
				success: function(data){
					var alertclassname = '', time = 2;
					switch (data.status){
						case 0: alertclassname = 'alert_success'; break;
						case 1: alertclassname = 'alert_error'; break;
					}
					$('#list_op_tips').attr('class', alertclassname);
					$('#list_op_tips').children('p').html(data.msg);
					$('#list_op_tips').fadeIn();
					obj.parent().parent('tr').remove();
					setTimeout( function(){
						$('#list_op_tips').fadeOut();
					}, ( time * 1000 ) );
				}
			});
		}
		return false;
	});

	$('#fileday').on('change', function(){
		month = $(this).val();
		getList( 1 );
	});
});
</script>

<script type="text/template" id="loglisttpl">
<tr>
	<td>${datetime}</td>
	<td>[${errorno}]</td>
	<td>${errorcontent}</td>
	<td>${errorfilepath}</td>
	<td style="color:red">${errorline}行</td>
	<td>&nbsp;</td>
</tr>
</script>

<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('log_title'); ?></span></a></li>
	</ul>
	<br class="clear">
	<div class="onecolumn">
		<div class="header">
			<h2><?php echo Lang('log_'.ROUTE_V.'_list') ?></h2>
			<ul class="second_level_tab" style="margin-bottom:0;">
				<li><a>请选择日期：</a></li>
				<li>
					<select name="fileday" id="fileday">
						<?php foreach ($data['filelist'] as $value) { ?>
						<option value="<?php echo $value ?>"><?php echo $value ?></option>
						<?php } ?>
					</select>
				</li>
			</ul>
		</div>

	</div>

	<div class="content">
		<!-- Begin example table data -->
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<thead>
			    <tr>
			    	<th><?php echo Lang('date') ?></th>
			    	<th><?php echo Lang('errorno') ?></th>
			    	<th><?php echo Lang('errorcontent') ?></th>
			    	<th><?php echo Lang('errorfilepath') ?></th>
			    	<th><?php echo Lang('errorline') ?></th>
			    	<th>&nbsp;</th>
			    </tr>
			</thead>
			<tbody id="loglist">
			   
			</tbody>
		</table>
		</div>
		<div class="pagination pager" id="pager">
		<div id="list_op_tips" style="display: none;"><p></p></div>
		<!-- End pagination -->	
	</div>		
</div>
