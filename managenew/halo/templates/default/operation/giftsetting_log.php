<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var giftlog = {pageIndex : 1, pageCount : 0, pageSize : 20}, recordNum = 0;
function showgiftlogList(data) {
	if (data.status == 0){
		recordNum = data.count;
		giftlog.pageCount = Math.ceil( data.count/giftlog.pageSize );
		$( "#giftlogpager" ).pager({ pagenumber: giftlog.pageIndex, pagecount: giftlog.pageCount, word: pageword, buttonClickCallback: getgiftlogList });
		$( "#giftloglist" ).empty();

		if (data.count > 0){
			$( "#giftloglisttpl" ).tmpl( data.list ).prependTo( "#giftloglist" );
			$( "#giftloglist" ).stop(true,true).hide().slideDown(400);
			if (giftlog.pageCount > 1){
				$( "#giftloglist" ).parent().parent('div.content').css('height', $('#giftloglist').parent('table.global').css('height'));
			}
		}
	}else {
		$('#giftloglist').html(data.msg);
	}
}
function getgiftlogList(index) {
	var query = "<?php echo INDEX; ?>?m=operation&c=giftsetting&v=ajax_giftsetting_log_list&top="+index+"&recordnum="+recordNum;
	giftlog.pageIndex = index;
	$( "#giftloglist" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			data: $('#get_giftlog_search_submit').serialize()+'&giftid=<?php echo $data['giftid']; ?>',
			success: function(data) {
				showgiftlogList(data);
			}
		});
	});
}
function sid_to_name(sid) {
	if (sid > 0 && typeof global_serverlist != 'undefined'){
		for (var key in global_serverlist) {
			if (global_serverlist[key].sid == sid) {
				return global_serverlist[key].name + '-' + global_serverlist[key].o_name;
			}
		}
	}
	return '';
}

$(function() {
	getgiftlogList(1);

	$('#get_giftlog_search_submit').on('submit', function(e) {
		e.preventDefault();
		recordNum=0;
		getgiftlogList(1);
	});
});
</script>

<script type="text/template" id="giftloglisttpl">
<tr>
	<td>${id}</td>
	<td>{{if player_id > 0}}${username}{{if nickname != ''}}(${nickname}){{/if}}{{else}}-{{/if}}</td>
	<td>${sid_to_name(sid)}</td>
	<td>${date('Y-m-d H:i', createtime)}</td>
	<td>${date('Y-m-d H:i', lastdotime)}</td>
	<td>${times}</td>
	<td>&nbsp;</td>
</tr>
</script>
<div id="bgwrap">
	<div class="nav singlenav">
		<form id="get_giftlog_search_submit" action="<?php echo INDEX; ?>?m=operation&c=giftsetting&v=ajax_giftsetting_log_list" method="get" name="form">
		<ul class="nav_li">
			<li class="nobg">
				<p>
					<select name="stype" id="">
						<option value="1">玩家昵称</option>
						<option value="3">玩家账号</option>
						<option value="2">玩家ID</option>
					</select>
					<input name="sname" type="text" value="" size="20">	
					<input type="submit" name="getsubmit" id="get_search_submit" value="搜索" class="button_link">
					<input name="dogetSubmit" type="hidden" value="1">
				</p>
			</li>
		</ul>
		</form>
	</div>
	<div class="content">
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th style="width:50px;">ID</th>
					<th style="width:300px;">领取玩家</th>
					<th style="width:150px;"><?php echo Lang('server'); ?></th>
					<th style="width:120px;">首次时间</th>
					<th style="width:120px;">最后时间</th>
					<th style="width:50px;"><?php echo Lang('times'); ?></th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="giftloglist">

			</tbody>
		</table>
	</div>
	<div class="pagination" id="giftlogpager"></div>
</div>
