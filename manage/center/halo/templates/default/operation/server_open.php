<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var pageIndex = 1, pageCount = 0, pageSize = 15, recordNum = 0;
var nowtime = '<?php echo time(); ?>';
function getList(index){
	var query = "<?php echo INDEX; ?>?m=develop&c=server&v=public_server_list&cid=-1&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#openedlist" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			success: showList
		});
	});
}
function showList( data, type) {
	if (data.status == -1){
		$('#openedlist').html(data.msg);
	}else {
		recordNum = data.count;
		pageCount = Math.ceil( data.count/pageSize );
		$( ".pager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getList });
		$( "#openedlist" ).empty();
		if (data.count > 0){
			$( "#serverlisttpl" ).tmpl( data.list ).prependTo( "#openedlist" );
			$( "#openedlist" ).stop(true,true).hide().slideDown(400);
			if (pageCount > 1){
				$( "#openedlist" ).parent().parent('div.content').css('height', $('#openedlist').parent('table.global').css('height'));
			}
		}
	}
}

function cid_to_name(cid) {
	if (cid > 0 && typeof global_companylist != 'undefined') {
		for(var key in global_companylist) {
			if (global_companylist[key].cid == cid) {
				return global_companylist[key].name;
			}
		}
	}
	return '';
}

$(function(){
	/**
	 * 运营平台
	 */
	setTimeout(function() {
		if (typeof global_companylist != 'undefined') {
			$('#global_companylisttpl').tmpl(global_companylist).appendTo('#cid');
		}

		getList(1);
	}, 250);

	$('#post_submit').on('submit', function(e){
		e.preventDefault();
		$('#btnsubmit').attr('disabled', 'disabled');
		var objform = $(this), cid = $('#cid').val(), name = $.trim($('#o_name').val()), server = $.trim($('#server').val());
		if (cid > 0 && name != '' && server != '') {
			$.ajax({
				url: '<?php echo INDEX; ?>?m=develop&c=server&v=setting',
				data: objform.serialize(),
				dataType: 'json',
				type: 'POST',
				success: function(data){
					var alertclassname = '', time = 2;
					switch (data.status){
						case 0: 
							alertclassname = 'alert_success'; 
							$('#serverlisttpl').tmpl(data.info).prependTo('#openedlist').fadeIn(2000, function(){
									var obj = $(this);
									obj.css('background', '#E6791C');
									setTimeout( function(){	obj.css('background', ''); }, ( 2000 ) );
								});	
							break;
						case 1: alertclassname = 'alert_error'; break;
					}
					$('#list_op_tips').attr('class', alertclassname);
					$('#list_op_tips').children('p').html(data.msg);
					$('#list_op_tips').fadeIn();
					setTimeout( function(){
						$('#list_op_tips').fadeOut();
						$('#btnsubmit').removeAttr('disabled');
					}, ( time * 1000 ) );
				},
				error: function (data) {
					$('#btnsubmit').removeAttr('disabled');
				}
			});
		}else {
			$('#list_op_tips').attr('class', 'alert_warning');
			$('#list_op_tips').children('p').html('<?php echo Lang('inter_everyone_columns')?>');
			$('#list_op_tips').fadeIn();
			setTimeout( function(){
				$('#list_op_tips').fadeOut();
				$('#btnsubmit').removeAttr('disabled');
			}, ( 2 * 1000 ) );
		}
	});
});
</script>

<script type="text/template" id="serverlisttpl">
<tr>
	<td>${sid}</td>
	<td>{{if open_date!=''}}${open_date}{{else}}&nbsp;{{/if}}</td>
	<td>${cid_to_name(cid)}</td>
	<td>{{if o_name!=''}}${o_name}{{else}}&nbsp;{{/if}}</td>
	<td>{{if server!=''}}{{html server.replace(/\|/i,'<br>')}}{{else}}&nbsp;{{/if}}</td>
	<td>{{if open == 1}}<span class='greentitle'><?php echo Lang('already_setting'); ?></span>{{else}}<span class="redtitle"><?php echo Lang('no_setting'); ?></span>{{/if}}</td>
	<td>{{if opendate < nowtime}}<span class="greentitle"><?php echo Lang('open'); ?></span>{{else}}<span class="redtitle"><?php echo Lang('donot_open'); ?></span>{{/if}}</td>
	<td>&nbsp;</td>
</tr>
</script>

<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('open_server_list') ?></span></a></li>
	</ul>
	<div class="clear"></div>

	<div class="content">
		<form name="post_submit" id="post_submit" action="<?php echo INDEX; ?>?m=develop&c=server&v=setting" method="post">
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th style="width:5%">&nbsp;</th>
					<th	style="width:8%"><?php echo Lang('server_date'); ?></th>
					<th style="width:8%"><?php echo Lang('company_platform'); ?></th>
					<th style="width:8%"><?php echo Lang('server_name'); ?></th>
					<th style="width:10%"><?php echo Lang('server_game_url'); ?></th>
					<th style="width:5%"><?php echo Lang('operation'); ?></th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th><?php echo Lang('add_new_record'); ?></th>
					<td><input type="text" name="open_date" value="<?php echo date('Y-m-d H:i:s'); ?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" readonly></td>
					<td>
						<select name="cid" id="cid">
							<option value="0"><?php echo Lang('company_platform'); ?></option>
						</select>
					</td>
					<td><input type="text" name="o_name" id="o_name" value="<?php echo Lang('da_hua_shen_xian')?>s"></td>
					<td><input type="text" name="server" id="server" value=""></td>
					<td>
						<input type="hidden" name="doflag" value="quick">
						<input type="hidden" name="doSubmit" value="1">
						<input type="submit" id="btnsubmit" class="button" value="<?php echo Lang('submit'); ?>"></td>
					<td>&nbsp;</td>
				</tr>
			</tbody>
		</table>
		</form>
		<div id="list_op_tips" style="display: none;"><p></p></div>
	</div>
	<br class="clear">
	<div class="content">
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th style="width:50px;">ID</th>
					<th	style="width:120px;"><?php echo Lang('server_date'); ?></th>
					<th style="width:8%"><?php echo Lang('company_platform'); ?></th>
					<th style="width:8%"><?php echo Lang('server_name'); ?></th>
					<th style="width:10%"><?php echo Lang('server_game_url'); ?></th>
					<th style="width:50px;"><?php echo Lang('status'); ?></th>
					<th style="width:50px;"><?php echo Lang('open_server'); ?></th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="openedlist">

			</tbody>
		</table>
	</div>
	<div class="pagination pager" id="pager"></div>
</div>
