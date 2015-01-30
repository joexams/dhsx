<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function(){
	$('#get_search_submit').on('submit', function(e) {
		e.preventDefault();
		obj = $(this);
		var url = '<?php echo INDEX; ?>?m=report&c=player&v=faction';
		Ha.common.ajax(url, 'json', obj.serialize(), 'get', 'container', function(data){
			if (data.status==0 && data.count > 0){
				$( "#factionlist" ).empty().append($( "#factionlisttpl" ).tmpl( data.list )).show();
			}else {
				$( "#factionlist").html('<tr><td colspan="13" style="text-align: left">没有找到帮派数据。</td></tr>');
			}
		}, 1);
		return false;
	});
	
	$('#get_search_submit').submit();
	/**
	 * 帮派成员
	 * @return {[type]} [description]
	 */
	$('#factionlist').on('click', 'a.member', function(){
		var obj = $(this), sid = '<?php echo $data["sid"] ?>', id = obj.attr('data-id'), title = obj.attr('data-name'), str = '';;
		if (id > 0 && sid > 0){
			var url = '<?php echo INDEX; ?>?m=report&c=player&v=faction_member';
			Ha.common.ajax(url, 'json', {sid: sid, faction_id: id}, 'get', 'container', function(data){
				if (data.status==0 && data.list.length > 0){
					$('#faction_member_list').html('');
					$('#faction_member_list_tpl').tmpl(data.list).appendTo('#faction_member_list');
					str = '';
					str = $('#faction_member_area').html();
				}else {
					str = '<h2><span id="tt">没有找到成员数据。</span></h2>';
				}
				Ha.Dialog.show(str, '【'+title + '】<?php echo Lang("faction_member") ?>', '', 'faction_member');
			}, 1);
		}
	});

	/**
	 * 帮派申请
	 * @return {[type]} [description]
	 */
	$('#factionlist').on('click', 'a.apply', function(){
		var obj = $(this), sid = '<?php echo $data["sid"] ?>', id = obj.attr('data-id'), title = obj.attr('data-name'), str = '';;
		if (id > 0 && sid > 0){
			var url = '<?php echo INDEX; ?>?m=report&c=player&v=faction_apply';
			Ha.common.ajax(url, 'json', {sid: sid, faction_id: id}, 'get', 'container', function(data){
				if (data.status==0 && data.list.length > 0){
					$('#faction_apply_list').html('');
					$('#faction_apply_list_tpl').tmpl(data.list).appendTo('#faction_apply_list');	
					str = '';
					str = $('#faction_apply_area').html();
				}else {
					str = '<h2><span id="tt">没有找到申请数据。</span></h2>';
				}
				Ha.Dialog.show(str, '【'+title + '】<?php echo Lang("faction_apply") ?>', 350, 'faction_apply');
			}, 1);
		}
	});
});
</script>

<script type="text/template" id="faction_member_list_tpl">
<tr>
	<td class="num">${id}</td>
	<td>{{if nickname != ''}}${nickname}{{else}}${username}{{/if}}</td>
	<td>${date('Y-m-d H:i',add_time)}</td>
	<td><span class="redtitle">${jobname}</span></td>
	<td>${contribution}</td>
	<td>${today_con}</td>
	<td>${date('Y-m-d H:i', last_con_time)}</td>
</tr>
</script>

<script type="text/template" id="faction_apply_list_tpl">
	<tr>
		<td class="num">${id}</td>
		<td>{{if nickname != ''}}${nickname}{{else}}${username}{{/if}}</td>
		<td>${date('Y-m-d H:i',req_time)}</td>
	</tr>
</script>

<script type="text/template" id="factionlisttpl">
<tr>
	<td class="num">${id}</td>
    <td>
    ${name}
    {{if group_number != '' && group_number != 0}}<br><strong class="greentitle">QQ:${group_number}</strong>{{/if}}
    </td>
    <td><span class="redtitle">${level}</span></td>
    <td>${master_name}</td>
    <td>{{if campname}}${campname}{{else}}--{{/if}}</td>
    <td>${member_count}</td>
    <td>${coins}</td>
    <td>${exp}</td>
    <td>${god_level}</td>
    <td>${god_exp}</td>
    <td>${now_week_con}</td>
    <td>${today_con}</td>
    <td><a href="javascript:;" class="member" data-id="${id}" data-name="${name}"><?php echo Lang('faction_member') ?></a></td>
    <td><a href="javascript:;" class="apply" data-id="${id}" data-name="${name}"><?php echo Lang('faction_apply') ?></a></td>
</tr>
</script>

<h2><span id="tt"><?php echo Lang('玩家数据'); ?>：
<a href="#app=4&cpp=35&url=<?php echo urlencode(WEB_URL.INDEX.'?m=report&c=player&v=init'); ?>"><?php echo Lang('game_server') ?></a>
<span>&gt;</span><?php echo $data['title']; ?>
<span>&gt;</span><?php echo Lang('faction'); ?></span></h2>
<div class="container" id="container">
	<?php include template('report', 'player_top'); ?>
	<div class="toolbar">
		<div class="tool_date cf">
			<form id="get_search_submit" action="" method="get" name="form">
			<div class="title cf">	
				<div class="tool_group">
					<label>等级到达：<input type="text" class="ipt_txt_s" name="level"></label>
					<label>帮派名称：<input type="text" class="ipt_txt" name="name"></label>
					<input type="hidden" name="sid" value="<?php echo $data['sid']; ?>">
					<input name="dogetSubmit" type="hidden" value="1">
					<input type="submit" class="btn_sbm" value="查询" id="query"> 
					<input type="reset" class="btn_rst" value="重置" id="reset">
				</div>
			</div>
			</form>
		</div>		
	</div>
	<div class="column cf" id="table_column">
	<div id="dataTable">
	<table>
	<thead>
	<tr id="dataTheadTr">
    	<th>&nbsp;</th>
	    <th><?php echo Lang('faction_name') ?></th>
	    <th><?php echo Lang('level') ?></th>
	    <th><?php echo Lang('faction_king') ?></th>
	    <th><?php echo Lang('camp') ?></th>
	    <th><?php echo Lang('person_num') ?></th>
	    <th><?php echo Lang('coins') ?></th>
	    <th><?php echo Lang('experience') ?></th>
	    <th><?php echo Lang('mar_level') ?></th>
	    <th><?php echo Lang('mar_experience') ?></th>
	    <th><?php echo Lang('weekly_contri') ?></th>
	    <th><?php echo Lang('dayly_contri') ?></th>
	    <th><?php echo Lang('faction_member') ?></th>
	    <th><?php echo Lang('faction_apply') ?></th>
	</tr>
	</thead>
	<tbody id="factionlist">
		   
	</tbody>
	</table>
	</div>
</div>
</div>

<div class="container" id="faction_apply_area" style="display:none">
	<div class="column cf">
		<div>
		<table>
		<thead>
		<tr>
	    	<th>&nbsp;</th>
		    <th><?php echo Lang('player') ?></th>
		    <th><?php echo Lang('apply_time') ?></th>
		</tr>
		</thead>
		<tbody id="faction_apply_list">
			   
		</tbody>
		</table>
		</div>
	</div>
</div>

<div class="container" id="faction_member_area" style="display:none">
	<div class="column cf">
		<div>
		<table>
		<thead>
		<tr>
	    	<th>&nbsp;</th>
		    <th><?php echo Lang('player') ?></th>
		    <th><?php echo Lang('join_time') ?></th>
		    <th><?php echo Lang('job') ?></th>
		    <th><?php echo Lang('contribution') ?></th>
		    <th><?php echo Lang('today_con') ?></th>
		    <th><?php echo Lang('last_con_time') ?></th>
		</tr>
		</thead>
		<tbody id="faction_member_list">
			   
		</tbody>
		</table>
		</div>
	</div>
</div>