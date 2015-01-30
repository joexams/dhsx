<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
Ha.page.recordNum = 0;
Ha.page.pageCount = 0;
Ha.page.pageSize = 15;
Ha.page.listEid = 'giftloglist';
Ha.page.colspan = 7;
Ha.page.emptyMsg = '<?php echo Lang('player_have_not_get_gift'); ?>';
Ha.page.url = "<?php echo INDEX; ?>?m=operation&c=giftsetting&v=ajax_giftsetting_log_list&giftid=<?php echo $data['giftid'];?>";
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
	Ha.page.getList(1);
	$('#get_giftlog_search_submit').on('submit', function(e) {
		e.preventDefault();
		Ha.page.recordNum = 0;
		Ha.page.queryData = $('#get_giftlog_search_submit').serialize();
		Ha.page.getList(1);
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
</tr>
</script>
<div class="container" id="container">
		<form id="get_giftlog_search_submit" method="get" name="form">
		<div class="frm_cont">
		<ul>
			<li>
					<select name="stype" id="" class="ipt_select">
						<option value="1"><?php echo Lang('player_nick'); ?></option>
						<option value="3"><?php echo Lang('player_name'); ?></option>
						<option value="2"><?php echo Lang('player'); ?>ID</option>
					</select>
					<input name="sname" type="text" value="" size="20" class="ipt_txt">	
					<input type="submit" name="getsubmit" id="get_search_submit" value="<?php echo Lang('find');?>" class="btn_sbm">
					<input name="dogetSubmit" type="hidden" value="1">
			</li>
		</ul>
		</div>
		</form>
		<div class="column cf" id="table_column">
	<div id="dataTable">
		<table>
			<thead>
				<tr>
					<th>ID</th>
					<th><?php echo Lang('get_player'); ?></th>
					<th><?php echo Lang('server'); ?></th>
					<th><?php echo Lang('first_time'); ?></th>
					<th><?php echo Lang('last_time'); ?></th>
					<th><?php echo Lang('times'); ?></th>
				</tr>
			</thead>
			<tbody id="giftloglist">

			</tbody>
		</table>
	</div>
	</div>
</div>
