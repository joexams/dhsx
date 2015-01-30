<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var dialog  = typeof dialog != 'undefined' ? null : '';
$(function(){
	// $('.dash1').on('click', 'a.othermenu', function(){
	// 	var obj = $(this), url = obj.attr('rel');
	// 	if (url != ''){
	// 		url = url + '&title=<?php echo $data["title"] ?>';
	// 		$('#container').load(url,function(response,status){});
	// 	}
	// });
	$('#get_search_submit').on('submit', function(e) {
		e.preventDefault();
		var level = $('#faction_level').val();
		var name = $('#faction_name').val();
		$.ajax({
			url: '<?php echo INDEX; ?>?m=server&c=get&v=player_faction_list',
			data: {sid: "<?php echo $data['sid']; ?>", level: level, name: name},
			dataType: 'json',
			success: function(data){
				if (data.status==0 && data.count > 0){
					$('#factionlist').empty();
					$('#factionlisttpl').tmpl(data.list).appendTo('#factionlist');
				}
			}
		});
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
			$.ajax({
				url: '<?php echo INDEX; ?>?m=server&c=get&v=player_faction_member',
				data: {sid: sid, faction_id: id},
				dataType: 'json',
				success: function(data){
					if (data.status == 0){
						dialog = $.dialog({id: 'faction_member', width: 600});
						$('#faction_member_list').html('');
						$('#faction_member_list_tpl').tmpl(data.list).appendTo('#faction_member_list');
						title = title + ' <?php echo Lang("faction_member") ?>'
						dialog.title(title);
						str = '';
						str = $('#faction_member_area').html();
						dialog.content(str);
					}
				}
			});
		}
	});

	/**
	 * 帮派申请
	 * @return {[type]} [description]
	 */
	$('#factionlist').on('click', 'a.apply', function(){
		var obj = $(this), sid = '<?php echo $data["sid"] ?>', id = obj.attr('data-id'), title = obj.attr('data-name'), str = '';;
		if (id > 0 && sid > 0){
			$.ajax({
				url: '<?php echo INDEX; ?>?m=server&c=get&v=player_faction_apply',
				data: {sid: sid, faction_id: id},
				dataType: 'json',
				success: function(data){
					if (data.status == 0){
						dialog = $.dialog({id: 'faction_apply', width: 400});
						$('#faction_apply_list').html('');
						$('#faction_apply_list_tpl').tmpl(data.list).appendTo('#faction_apply_list');
						title = title + ' <?php echo Lang("faction_apply") ?>'
						dialog.title(title);
						str = '';
						str = $('#faction_apply_area').html();
						dialog.content(str);
					}
				}
			});
		}
	});
});
</script>

<script type="text/template" id="faction_member_list_tpl">
<tr>
	<td>${id}</td>
	<td>${username}{{if nickname != ''}} (${nickname}){{/if}}</td>
	<td>${date('Y-m-d H:i',add_time)}</td>
	<td>${jobname}</td>
	<td>${contribution}</td>
	<td>${today_con}</td>
	<td>${date('Y-m-d H:i', last_con_time)}</td>
	<td>&nbsp;</td>
</tr>
</script>

<script type="text/template" id="faction_apply_list_tpl">
	<tr>
		<td>${id}</td>
		<td>${username}{{if nickname != ''}} (${nickname}){{/if}}</td>
		<td>${date('Y-m-d H:i',req_time)}</td>
		<td>&nbsp;</td>
	</tr>
</script>

<script type="text/template" id="factionlisttpl">
<tr>
	<td>${id}</td>
    <td>
    ${name} (LV.${level})
    {{if group_number != '' && group_number != 0}}<br><strong>QQ:${group_number}</strong>{{/if}}
    </td>
    <td>${master_name}</td>
    <td>${campname}</td>
    <td>${member_count}</td>
    <td>${coins}</td>
    <td>${exp}</td>
    <td>${god_level}</td>
    <td>${god_exp}</td>
    <td>${now_week_con}</td>
    <td>${today_con}</td>
    <td><a href="javascript:;" class="member" data-id="${id}" data-name="${name}"><?php echo Lang('faction_member') ?></a></td>
    <td><a href="javascript:;" class="apply" data-id="${id}" data-name="${name}"><?php echo Lang('faction_apply') ?></a></td>
    <td><?php echo Lang('view'); ?></td>
    <td><?php echo Lang('view'); ?></td>
	<td>&nbsp;</td>
</tr>
</script>

<div class="content" style="display:none" id="faction_apply_area">
	<!-- Begin form elements -->
	<table class="global" width="100%" cellpadding="0" cellspacing="0">
		<thead>
			<tr>
				<th style="width:50px">ID</th>
			    <th style="width:350px"><?php echo Lang('player') ?></th>
			    <th style="width:120px"><?php echo Lang('apply_time') ?></th>
			    <th>&nbsp;</th>
			</tr>
		</thead>
		<tbody id="faction_apply_list">

		</tbody>
	</table>
<!-- End form elements -->
</div>

<div class="content" style="display:none" id="faction_member_area">
	<!-- Begin form elements -->
	<table class="global" width="100%" cellpadding="0" cellspacing="0">
		<thead>
			<tr>
				<th style="width:50px">ID</th>
			    <th style="width:150px"><?php echo Lang('player') ?></th>
			    <th style="width:120px"><?php echo Lang('join_time') ?></th>
			    <th style="width:80px"><?php echo Lang('job') ?></th>
			    <th style="width:80px"><?php echo Lang('contribution') ?></th>
			    <th style="width:80px"><?php echo Lang('today_con') ?></th>
			    <th style="width:120px"><?php echo Lang('last_con_time') ?></th>
			    <th>&nbsp;</th>
			</tr>
		</thead>
		<tbody id="faction_member_list">

		</tbody>
	</table>
<!-- End form elements -->
</div>


<div id="bgwrap">
	<div class="locationbq">
        <div class="locLeft">
            <a href="#app=5&cpp=24&url=<?php echo urlencode(WEB_URL.INDEX.'?m=report&c=player&v=init'); ?>"><?php echo Lang('game_server') ?></a>
            <span>&gt;</span><?php echo $data['title']; ?>
            <span>&gt;</span><?php echo Lang('faction') ?>
        </div>
        <div class="logo"></div>
    </div>
	<ul class="dash1">
		<li class="fade_hover bubble"><a href="<?php echo $url1 ?>" rel="<?php echo INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=detail_list&sid='.$data['sid'].'&cid='.$data['cid'].'' ?>" class="othermenu"><span><?php echo Lang('player_list') ?></span></a></li>
		<li class="fade_hover selected"><a href="<?php echo $url2 ?>"><span><?php echo Lang('faction') ?></span></a></li>
		<li class="fade_hover bubble"><a href="<?php echo $url3 ?>" rel="<?php echo INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=arena&sid='.$data['sid'].'&cid='.$data['cid'].'' ?>" class="othermenu"><span><?php echo Lang('arena') ?></span></a></li>
		<li class="fade_hover"><a href="<?php echo $url4 ?>" rel="<?php echo INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=gamelog&sid='.$data['sid'].'&cid='.$data['cid'].'' ?>" class="othermenu"><span><?php echo Lang('game_log') ?></span></a></li>
		<li class="fade_hover"><a href="<?php echo $url5 ?>" rel="<?php echo INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=gamewar&sid='.$data['sid'].'&cid='.$data['cid'].'' ?>" class="othermenu"><span>群仙会</span></a></li>
	</ul>
	<br class="clear">
	<div class="nav singlenav">
		<form id="get_search_submit" action="<?php echo INDEX; ?>?m=server&c=get&v=player_faction_list" method="get" name="form">
		<ul class="nav_li">
			<li>
				<p>
					等级到达：<input type="text" name="level" id="faction_level" value="0" >
					帮派名称：<input type="text" name="name" id="faction_name" value="" >
					
					<input type="submit" name="getsubmit" id="get_search_submit" value="<?php echo Lang('search'); ?>" class="button_link">
					<input name="dogetSubmit" type="hidden" value="1">
				</p>
			</li>
		</ul>
		</form>
	</div>

	<div class="content">
		<!-- Begin form elements -->
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th style="width:50px;">ID</th>
				    <th style="width:100px;"><?php echo Lang('faction_name') ?></th>
				    <th style="width:50px"><?php echo Lang('faction_king') ?></th>
				    <th style="width:50px"><?php echo Lang('camp') ?></th>
				    <th style="width:50px"><?php echo Lang('person_num') ?></th>
				    <th style="width:50px"><?php echo Lang('coins') ?></th>
				    <th style="width:50px"><?php echo Lang('experience') ?></th>
				    <th style="width:50px"><?php echo Lang('mar_level') ?></th>
				    <th style="width:50px"><?php echo Lang('mar_experience') ?></th>
				    <th style="width:100px"><?php echo Lang('weekly_contri') ?></th>
				    <th style="width:100px"><?php echo Lang('dayly_contri') ?></th>
				    <th style="width:50px"><?php echo Lang('faction_member') ?></th>
				    <th style="width:50px"><?php echo Lang('faction_apply') ?></th>
				    <th style="width:80px"><?php echo Lang('faction_description') ?></th>
				    <th style="width:80px"><?php echo Lang('faction_notice') ?></th>
				    <th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="factionlist">

			</tbody>
		</table>
	<!-- End form elements -->
	</div>
</div>
