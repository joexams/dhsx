<?php defined('IN_G') or exit('No permission resources.'); ?>
<div id="bgwrap">
	<div class="locationbq">
        <div class="locLeft">
            <a href="#app=5&cpp=24&url=<?php echo urlencode(WEB_URL.INDEX.'?m=report&c=player&v=init'); ?>"><?php echo Lang('game_server') ?></a>
            <span>&gt;</span><a href="#app=5&cpp=24&url=<?php echo urlencode(WEB_URL.INDEX.'?m=report&c=player&v=detail_list&sid='.$data['sid'].'&title='.$data['title'])?>"><?php echo $data['title']; ?></a>
            <span>&gt;</span><?php echo Lang('player_detail') ?>
        </div>
        <div class="logo"></div>
    </div>

	<ul class="dash1" id="player_info">
		<li class="fade_hover selected"><a href="javascript:;"><span>玩家详情</span></a></li>
	</ul>
	<br class="clear">
	<div class="onecolumn">
		<div class="header">
			<ul class="second_level_tab">
				<li><a href="javascript:;" id="pop_refresh"><?php echo Lang('refresh'); ?></a></li>
			</ul>
		</div>
	</div>
	<br class="clear">
	<div class="nav singlenav">
		<form id="get_search_submit" action="<?php echo INDEX; ?>?m=server&c=get&v=player_faction_list" method="get" name="form">
		<ul class="nav_li">
			<li>
				<p>
					玩家ID：<input type="text" name="player_id" id="player_id" value="<?php echo $data['id']; ?>" >
					玩家账号：<input type="text" name="playername" id="player_name" value="" >
					玩家昵称：<input type="text" name="nickname" id="nickname" value="" >
	
					<input type="submit" name="getsubmit" id="get_search_btn" value="<?php echo Lang('search'); ?>" class="button_link">
					<input name="dogetSubmit" type="hidden" value="1">
				</p>
			</li>
		</ul>
		</form>
	</div>
	<br class="clear">
	<div class="content">
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td style="width:33%;text-align:center" id="playername">
					
				</td>
				<td>
					<ul id="blockinfolist" class="b_lib_nav"></ul>
				</td>
			</tr>
		</table>
	</div>
	<br class="clear">

	<div class="content" id="player_detail_info">
		
	</div>

	<br class="clear"/>
	<ul id="blockloglist" class="b_lib_nav"></ul>
	<br class="clear"/>
    <div id="logarea" class="content" style="display:none;border:solid 2px #e6791c;padding:10px 0;"></div>
    <br class="clear"/>	
	<div class="onecolumn">
		<div class="header">
			<h2><?php echo Lang('伙伴 (点伙伴名查看装备)'); ?></h2>
			<ul class="second_level_tab"></ul>
		</div>
	</div>
	<div class="content">
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th>ID</th>
					<th>伙伴名称</th>
					<th>经验</th>
					<th>生命值<br>上限</th>
					<th>攻击<br>防御</th>
					<th>法术攻<br>法术防</th>
					<th>绝技攻<br>绝技防</th>
					<th>
						暴击/闪避/命中/格挡
						<br>破档/破暴击/必杀
					</th>
					<th>武力</th>
					<th>绝技</th>
					<th>法术</th>
					<th>渡劫</th>
					<th>命格</th>
					<th>丹药记录</th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="partnerlist">
				
			</tbody>
		</table>
	</div>
</div>
<br class="clear">
<script type="text/javascript">
var blockloglist = <?php echo $data['blockloglist']; ?>;
var blockinfolist = <?php echo $data['blockinfolist']; ?>;

var dialog1 = typeof dialog != 'undefined' ? dialog : '';
var dialog  = typeof dialog != 'undefined' ? null : '';

var playerid = "<?php echo $data['id']; ?>", playername = '';
var nickname = '';
var follow_role_id = 0;
var rolelist;
var deploy;
var partner;
function role_id_to_name(role_id) {
	if (rolelist.length > 0) {
		for(var key in rolelist) {
			if (rolelist[key].id == role_id) {
				return rolelist[key].name;
			}
		}
	}
	return '';
}
function player_role_id_to_name(player_role_id) {
	if (partner.length > 0) {
		for(var key in partner) {
			if (partner[key].id == player_role_id) {
				return role_id_to_name(partner[key].role_id);
			}
		}
	}
	return '';
}
function deploy_id_to_name(deploy_id) {
	if (deploy.length > 0) {
		for(var key in deploy) {
			if (deploy[key].id == deploy_id) {
				return deploy[key].name;
			}
		}
	}
	return '';
}
$(document).ready(function(){
	$('#blocktpl').tmpl(blockloglist).prependTo('#blockloglist');
	$('#blocktpl').tmpl(blockinfolist).prependTo('#blockinfolist');
	blockloglist = null;
	blockinfolist = null;
    delete dialog;
	$('#blockloglist').on('click', 'a.block_record', function() {
		var key = $(this).attr('data-key'), title=encodeURI(playername);
		if (key != ''){
			/*dialog = $.dialog({id: 'pop_'+"<?php echo $data['id'] ?>", title: title, width:960});
			$.ajax({
				url: '<?php echo INDEX; ?>?m=report&c=player&v=record',
				data: "sid=<?php echo $data['sid'] ?>&id=<?php echo $data['id']; ?>&version=<?php echo $data['version']; ?>&key="+key+"&title="+title+'&nickname='+nickname,
				success: function(data){
					dialog.content(data);
				}
			});
            */
            var url = '<?php echo INDEX; ?>?m=report&c=player&v=record&sid=<?php echo $data['sid'] ?>&id=<?php echo $data['id']; ?>&version=<?php echo $data['version']; ?>&key='+key+'&title='+title+'&nickname='+nickname;
			$('#logarea').show();
            $('#logarea').load(url,function(response,status){});
		}
		return false;
	});

	$('#blockloglist').on('click', 'a.other', function(){
		var title="<?php echo Lang('player'); ?>"+playername+"-><?php echo Lang('player_bug'); ?>";
		dialog = $.dialog({id: 'pop_'+"<?php echo $data['id'] ?>", title: title, width:960});
		$.ajax({
			url: '<?php echo INDEX; ?>?m=operation&c=interactive&v=gm',
			data: "sid=<?php echo $data['sid']; ?>&title="+encodeURI(playername),
			success: function(data){
				dialog.content(data);
			}
		});
		dialog.title(title);
		return false;
	});

	$('#blockinfolist').on('click', 'a.block_info', function(){
		var key = $(this).attr('data-key'), title= playername;
		title = encodeURI(title);
		if (key != ''){
			dialog = $.dialog({id: 'pop_'+"<?php echo $data['id']; ?>", title: title, width:800});
			$.ajax({
				url: '<?php echo INDEX; ?>?m=report&c=player&v=info',
				data: "sid=<?php echo $data['sid'] ?>&id=<?php echo $data['id']; ?>&version=<?php echo $data['version']; ?>&key="+key+"&title="+title,
				success: function(data){
					dialog.content(data);
				}
			});
		}
		return false;
	});

	$('#partnerlist').on('click', 'a.partner', function() {
		var key = $(this).attr('data-key'), player_role_id = $(this).attr('data-id'), title= playername;
		title = encodeURI(title);
		if (key != ''){
			dialog = $.dialog({id: 'pop_'+"<?php echo $data['id']; ?>", title: title, width:800});
			$.ajax({
				url: '<?php echo INDEX; ?>?m=report&c=player&v=info',
				data: {sid: <?php echo $data['sid']; ?>, id: <?php echo $data['id']; ?>, version: '<?php echo $data['version']; ?>', player_role_id: player_role_id, key: key, title: title},
				success: function(data){
					dialog.content(data);
				}
			});
		}
		return false;
	});

	$.ajax({
		url: '<?php echo INDEX; ?>?m=server&c=get&v=player_detail_info',
		data: "sid=<?php echo $data['sid'] ?>&id=<?php echo $data['id']; ?>&version=<?php echo $data['version']; ?>",
		dataType: 'json',
		success: function(data){
			if (data.status == 0){
				playername = data.info.username;
				nickname = data.info.nickname;
				follow_role_id = data.info.follow_role_id;
				rolelist = data.role;
				partner = data.partner;
				deploy = data.deploy;
				var title = '<?php echo Lang('player'); ?>'+playername;
				document.title = title+'-'+ '<?php echo Lang('title'); ?>';

				var strplayer = '<strong>'+playername + '</strong>';
				strplayer += nickname != '' ? ' <br><span class="bluetitle">' + nickname + '</span> ' : '';
				switch(data.info.is_tester) {
					case '1':
						strplayer += '(<span class="graytitle">测试号</span>)';
						break;
					case '2':
						strplayer += '(<span class="graytitle">高级测试号</span>)';
						break;
					case '3':
						strplayer += '(<span class="graytitle">GM</span>)';
						break;
					case '4':
						strplayer += '(<span class="graytitle">指导员号</span>)';
						break;
				}
				$('#playername').html(strplayer);
				//$('#player_info').find('.selected span').html(title);
				$('#detail_info_tpl').tmpl(data.info).appendTo('#player_detail_info');


				$('#partnerlist_tpl').tmpl(partner).appendTo('#partnerlist');
			}
		}
	});

	$('#get_search_submit').on('submit', function(e){
		e.preventDefault();
		var pname = $('#player_name').val(), 
			nname = $('#nickname').val(),
			pid = !isNaN(parseInt($('#player_id').val())) ? parseInt($('#player_id').val()) : 0,
			hashurl = 'app=5&cpp=24&url='+encodeurl('report', 'detail_info', 'player',"&sid=<?php echo $data['sid']; ?>&sname=<?php echo $data['title']; ?>&id=");
		if (pid > 0) {
			location.hash = hashurl+''+pid;
		}else {
			$.ajax({
				url: '<?php echo INDEX; ?>?m=server&c=get&v=player_id',
				data: "sid=<?php echo $data['sid'] ?>&playername="+pname+"&nickname="+nname,
				dataType: 'json',
				success: function(data){
					if (data.status == 0) {
						location.hash = hashurl+data.id;
					}
				},
				error: function() {}
			});
		}
	
	});


	$('#pop_refresh').on('click', function(){
		if (dialog1 === ''){
			location.reload();
		}else {
			var url = window.location;
			$.ajax({
				url: url,
				dataType: 'html',
				success: function(data){
					dialog1.content($(data).find('#container').html());
				}
			});
		}
		
		return false;
	});
});
</script>

<script type="text/template" id="partnerlist_tpl">
<tr>
	<td>${id}</td>
	<td>
	<p style="float:left;">
	<a href="javascript:;" data-key="item" data-id="${id}" class="partner" title="查看装备">${role_id_to_name(role_id)}</a>  Lv.${level}
	{{if trans_player_role_id > 0}}<br><span class="bluetitle">传承给：${player_role_id_to_name(trans_player_role_id)}</span>{{/if}}
	{{if be_trans_player_role_id > 0}}<br><span class="bluetitle">被传承：${player_role_id_to_name(be_trans_player_role_id)}</span>{{/if}}
	</p>
	<p style="float:right;">
	{{if  id == follow_role_id}}<span class="bluetitle">跟随</span><br>{{else}}{{if deploy_mode_id>0}}上阵<br>{{/if}}{{/if}}
	{{if state==0}}正常{{else}}<span class="graytitle">离队</span>{{/if}}
	</p>
	</td>
	<td>${experience}</td>
	<td>
	<span class="redtitle">${health}</span><br>
	<span class="greentitle"><span class="greentitle">${max_health}</span></td>
	<td>
	<span class="redtitle">${attack}</span><br>
	<span class="greentitle">${defense}</span></td>
	<td>
	<span class="redtitle">${magic_attack}</span>
	<br>
	<span class="greentitle">${magic_defense}</span></td>
	<td>
	<span class="redtitle">${stunt_attack}</span>
	<br>
	<span class="greentitle">${stunt_defense}</span></td>
	<td>
	${critical}/${dodge}/${hit}/${block}
	<br>${break_block}/${break_critical}/${kill}
	</td>
	<td>${strength} 
	<br>
	<span class="greentitle">+${strength_additional}</span> <span class="greentitle">+${mission_strength}</span></td>
	<td>${agile} 
	<br>
	<span class="greentitle">+${agile_additional}</span> <span class="greentitle">+${mission_agile}</span></td>
	<td>${intellect} 
	<br>
	<span class="greentitle">+${intellect_additional}</span> <span class="greentitle">+${mission_intellect}</span></td>
	<td>{{if spirit_name != null}}${spirit_name} Lv.${spirit_lv}{{else}}-{{/if}}</td>
	<td><a href="javascript:;" data-key="fate" data-id="${id}" class="partner" title="查看命格">命格${can_wear_fate_number}</a></td>
	<td><a href="javascript:;" data-key="role_elixir" data-id="${id}" class="partner" title="查看丹药记录">丹药记录</a></td>
	<td>&nbsp;</td>
</tr>
</script>

<script type="text/template" id="blocktpl">
<li><a href="javascript:;" class="{{if block == '<?php echo $data['blocklog'] ?>'}}block_record{{else}}block_info{{/if}}" data-key="${key}">${name}</a></li>
</script>

<script type="text/template" id="detail_info_tpl">

<table class="global nowrap" width="100%" cellpadding="0" cellspacing="0" style="min-width: 100%; ">
	<tbody>
		<tr>
			<td>
			<table class="global nowrap" width="100%" cellpadding="0" cellspacing="0" style="min-width: 100%; ">
			<tbody>
				<tr>
					<th style="width:30%">vip<?php echo Lang('level'); ?></th>
					<td style="width:30%"><strong>${vip_level}</strong></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<th><?php echo Lang('sport_ranking'); ?></th>
					<td>${ranking?ranking:'-'}</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<th><?php echo Lang('faction_name'); ?></th>
					<td>{{if factionname != null}}${factionname}{{else}}-{{/if}}</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<th><?php echo Lang('命格背包数'); ?></th>
					<td>{{if fate_grid_number != null}}${fate_grid_number}{{else}}-{{/if}}</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<th><?php echo Lang('绝技'); ?></th>
					<td>-</td>
					<td>&nbsp;</td>
				</tr>
			</tbody>
			</table>
			</td>

			<td>
			<table class="global nowrap" width="100%" cellpadding="0" cellspacing="0" style="min-width: 100%; ">
				<tbody>
					<tr>
						<th style="width:30%"><?php echo Lang('ingot'); ?></th>
						<td style="width:30%"><span class="orangetitle">${ingot}</span></td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('to_this_level_charge_ingot'); ?></th>
						<td>${ingot_vip ? ingot_vip : '-'}</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('升级到当前级别时间'); ?></th>
						<td>${level_up_time ? date('Y-m-d H:i', level_up_time) : '-'}</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('charge_ingot_total'); ?></th>
						<td>${charge_ingot > 0 ? charge_ingot : '-'}</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('over_recharge_ingot'); ?></th>
						<td>${total_ingot? total_ingot : '-'}</td>
						<td>&nbsp;</td>
					</tr>
				</tbody>
			</table>
			</td>


			<td>
			<table class="global nowrap" width="100%" cellpadding="0" cellspacing="0" style="min-width: 100%; ">
				<tbody>
					<tr>
						<th style="width:30%"><?php echo Lang('coins'); ?></th>
						<td style="width:30%">${coins}</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('prestige'); ?></th>
						<td>${fame} (Lv.${fame_level})</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('skill'); ?></th>
						<td>${skill}</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('player_state_point'); ?></th>
						<td>${state_point?state_point:'-'}</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('武魂'); ?></th>
						<td>-</td>
						<td>&nbsp;</td>
					</tr>
				</tbody>
			</table>
			</td>
		</tr>

		<tr>
			<td>
			<table class="global nowrap" width="100%" cellpadding="0" cellspacing="0" style="min-width: 100%; ">
				<tbody>
					<tr>
						<th style="width:30%"><?php echo Lang('power'); ?></th>
						<td style="width:30%">${power}</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('other_power'); ?></th>
						<td>${extra_power}</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('today_pay_power'); ?></th>
						<td>${buy_power_times_today}</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('default_deploy_mode'); ?></th>
						<td>${deploy_id_to_name(deploy_mode_id)}</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('桃树等级'); ?></th>
						<td>${peach_lv}</td>
						<td>&nbsp;</td>
					</tr>
				</tbody>
			</table>
			</td>

			<td>
			<table class="global nowrap" width="100%" cellpadding="0" cellspacing="0" style="min-width: 100%; ">
				<tbody>
					<tr>
						<th style="width:30%"><?php echo Lang('十二宫等级'); ?></th>
						<td style="width:30%">${zodiac_level?zodiac_level:'-'}</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('十二宫关卡'); ?></th>
						<td>${barrier?barrier:'-'}</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('last_get_faction_salary_date'); ?></th>
						<td>${date('Y-m-d H:i', get_faction_salary_last_date)}</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('travel_event_times'); ?></th>
						<td>${travel_event_join_count}</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('last_travel_event_time'); ?></th>
						<td>${date('Y-m-d H:i', travel_event_last_time)}</td>
						<td>&nbsp;</td>
					</tr>
				</tbody>
			</table>
			</td>

			<td>
			<table class="global nowrap" width="100%" cellpadding="0" cellspacing="0" style="min-width: 100%; ">
				<tbody>
					<tr>
						<th style="width:30%"><?php echo Lang('last_refresh_lucky_shop_time'); ?></th>
						<td style="width:30%">${date('Y-m-d H:i', last_refresh_lucky_shop_time)}</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('is_auto_rune'); ?></th>
						<td>{{if is_auto_rune>0}}YES{{else}}NO{{/if}}</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('last_increase_time'); ?></th>
						<td>${date('Y-m-d H:i', last_increase_time)}</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('爬塔最高道数'); ?></th>
						<td>${travel_event_join_count}</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('爬塔最高层数'); ?></th>
						<td>${date('Y-m-d H:i', travel_event_last_time)}</td>
						<td>&nbsp;</td>
					</tr>
				</tbody>
			</table>
			</td>
		</tr>
	</tbody>
</table>
</script>
