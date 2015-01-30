<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var playerid = "<?php echo $data['id']; ?>", playername = '';
var nickname = '';

$(function(){

	$('.first_level_tab').on('click', 'a.playertype', function(){
		$('.current', $('.first_level_tab')).removeClass('current');
		$(this).parent().addClass('current');
		var type = $(this).attr('data-type');
		$('div[id^=table_column_]', $('#container')).each(function(){
			$(this).hide()
		});
		$('#table_column_'+type).show();
	});

	$('#blockloglist').on('click', 'a.block_record', function() {
		var key = $(this).attr('data-key'), title=encodeURI(playername);
		if (key != ''){
            var url = '<?php echo INDEX; ?>?m=report&c=player&v=record';
            var queryData = 'sid=<?php echo $data['sid'] ?>&id=<?php echo $data['id']; ?>&version=<?php echo $data['version']; ?>&key='+key+'&title='+title+'&nickname=<?php echo $data['info']['nickname'] ?>';
            $('.active', $('#blockloglist')).removeClass('active');
            $(this).addClass('active');
			Ha.common.ajax(url, 'html', queryData, 'get', 'table_column_3', function(data){
				$('#logarea').empty().html(data).show();
			}, 1);
		}
		return false;
	});

	$('#blockloglist').on('click', 'a.other', function(){
		var title="<?php echo Lang('player'); ?>"+playername+"-><?php echo Lang('player_bug'); ?>";
		var url = '<?php echo INDEX; ?>?m=operation&c=interactive&v=gm';

		$('.active', $('#blockloglist')).removeClass('active');
        $(this).addClass('active');

		Ha.common.ajax(url, 'html', "sid=<?php echo $data['sid']; ?>&title="+encodeURI(playername), 'get', 'container', function(data){
			Ha.Dialog.show(data, title, '', 'pop_'+"<?php echo $data['id'] ?>");
		}, 1);
		return false;
	});

	$('#blockinfolist').on('click', 'a.block_info', function(){
		var key = $(this).attr('data-key'), title= playername;
		title = encodeURI(title);
		if (key != ''){
			$('.active', $('#blockinfolist')).removeClass('active');
        	$(this).addClass('active');

			var url = '<?php echo INDEX; ?>?m=report&c=player&v=info';
			var queryData = "sid=<?php echo $data['sid'] ?>&id=<?php echo $data['id']; ?>&version=<?php echo $data['version']; ?>&key="+key+"&title="+title;
			Ha.common.ajax(url, 'html', queryData, 'get', 'table_column_2', function(data){
				$('#infoarea').empty().html(data).show();
			}, 1);
		}
		return false;
	});

	$('#partnerlist').on('click', 'a.partner', function() {
		var key = $(this).attr('data-key'), player_role_id = $(this).attr('data-id'), title= playername;
		title = encodeURI(title);
		if (key != ''){
			var url = '<?php echo INDEX; ?>?m=report&c=player&v=info';
			var queryData = {sid: <?php echo $data['sid']; ?>, id: <?php echo $data['id']; ?>, version: '<?php echo $data['version']; ?>', player_role_id: player_role_id, key: key, title: title};
			Ha.common.ajax(url, 'html', queryData, 'get', 'container', function(data){
				Ha.Dialog.show(data, title, '', 'pop_'+"<?php echo $data['id'] ?>");
			}, 1);
		}
		return false;
	});

	$('#get_search_submit').on('submit', function(e){
		e.preventDefault();
		var pname = $('#player_name').val(), 
			pid = !isNaN(parseInt($('#player_name').val())) ? parseInt($('#player_name').val()) : 0,
			hashurl = 'app=4&cpp=35&url='+encodeurl('report', 'player_info', 'player',"&sid=<?php echo $data['sid']; ?>&sname=<?php echo $data['title']; ?>&id=");
		if (pid > 0) {
			location.hash = hashurl+''+pid;
		}else {
			var url = '<?php echo INDEX; ?>?m=report&c=player&v=public_player_id';
			Ha.common.ajax(url, 'json', "sid=<?php echo $data['sid'] ?>&username="+pname, 'get', 'container', function(data){
				if (data.status == 0) {
					location.hash = hashurl+data.id;
				}else {
					Ha.notify.show('没有找到此玩家', '', 'error');
				}
			}, 1);
		}
	});

	$('#ajax-edit-nickname').on('click', function(e){
		e.preventDefault();
		var ipt_nickname = trim($('#ipt_nickname').val());
		if (ipt_nickname != '' && ipt_nickname != '<?php echo $data['info']['nickname'] ?>') {
			var url = '<?php echo INDEX; ?>?m=report&c=player&v=set_nickname';
			var queryData = 'sid=<?php echo $data['sid'] ?>&player_id=<?php echo $data['id']; ?>&new_nickname='+ipt_nickname+'&nickname=<?php echo $data['info']['nickname'] ?>';
			Ha.common.ajax(url, 'json', queryData, 'post');
		}else {
			Ha.notify.show('操作失败，昵称未改变或为空！');
		}
	});
});
</script>

<h2><span id="tt">玩家详情：
<a href="#app=4&cpp=35&url=<?php echo urlencode(WEB_URL.INDEX.'?m=report&c=player&v=init'); ?>"><?php echo Lang('game_server') ?></a>
<span>&gt;</span><a href="#app=4&cpp=35&url=<?php echo urlencode(WEB_URL.INDEX.'?m=report&c=player&v=player_list&sid='.$data['sid'].'&title='.$data['title'])?>"><?php echo $data['title']; ?></a>
</span></h2>
<div class="container" id="container">
	<div class="toolbar">
		<div class="tool_date">
			<div class="title cf">
				<form id="get_search_submit" method="get" name="form">
				<div class="tool_group">
					 <label>玩家：
						<input type="text" name="username" id="player_name" value="" class="ipt_txt"></label>
					<input type="hidden" name="dogetSubmit" value="1">
					<input type="submit" class="btn_sbm" value="切 换">
				</div>
				</form>
			</div>
		</div>
	</div>
	<div class="speed_result">
		<div class="report">
			<div class="report_cont">
			    <ul>
			    <li>
			    	<span class="thin1">玩家ID：</span><label><?php echo $data['info']['id']; ?></label>
			    	<span class="thin1">玩家账号：</span><?php echo $data['info']['username']; ?></label>
			    	<span class="thin1">玩家昵称：</span><?php echo $data['info']['nickname']; ?></label>
			    	<?php if (!$limit) { ?>
			    	<label id="tester">
			    		<?php if ($data['info']['is_tester'] == 1) {  ?>
			    		<a class="redtitle">测试号</a>
			    		<?php }elseif ($data['info']['is_tester'] == 2) { ?>
			    		<a class="redtitle">高级测试号</a>
			    		<?php }elseif ($data['info']['is_tester'] == 3) { ?>
			    		<a class="redtitle">GM</a>
			    		<?php }elseif ($data['info']['is_tester'] == 4) { ?>
			    		<a class="redtitle">指导员号</a>
			    		<?php } ?>
			    	</label>
			    	<?php } ?>
			    	<span class="thin1">注册：<?php echo date('Y-m-d H:i:s', $data['info']['first_login_time'])?>/<?php echo $data['info']['first_login_ip'] ?></span>
			    	<span class="thin1">最后登录：<?php echo date('Y-m-d H:i:s', $data['info']['last_login_time'])?>/<?php echo $data['info']['last_login_ip'] ?></span>
			    </li>
				</ul>
			</div>
		</div>
	    <div class="mod_tab_title first_level_tab">
	    	<ul>
	    		<li class="current"><a href="javascript:void(0);" data-type="0" class="playertype">基本信息</a></li>
	    		<li><a href="javascript:void(0);" data-type="1" class="playertype">伙伴</a></li>
	    		<li><a href="javascript:void(0);" data-type="2" class="playertype">装备·背包</a></li>
	    		<li><a href="javascript:void(0);" data-type="3" class="playertype">日志记录</a></li>
	    		<li><a href="javascript:void(0);" data-type="4" class="playertype">修改昵称</a></li>
	    	</ul>
	    </div>
	</div>
	<div class="column cf" id="table_column_0">
		<div>
			<table>
			<tbody>
				<tr>
					<td>
					<table>
						<tbody>
						<tr>
							<td>vip<?php echo Lang('level'); ?></td>
							<td><strong><?php echo $data['info']['vip_level'] ?></strong></td>
						</tr>
						<tr>
							<td><?php echo Lang('sport_ranking'); ?></td>
							<td><?php echo $data['info']['ranking'] ? $data['info']['ranking'] : '-' ?></td>
						</tr>
						<tr>
							<td><?php echo Lang('faction_name'); ?></td>
							<td><?php echo $data['info']['factionname'] ? $data['info']['factionname'] : '-' ?></td>
						</tr>
						<tr>
							<td><?php echo Lang('命格背包数'); ?></td>
							<td><?php echo $data['info']['fate_grid_number'] ? $data['info']['fate_grid_number'] : '-' ?></td>
						</tr>
						<tr>
							<td><?php echo Lang('绝技'); ?></td>
							<td>-</td>
						</tr>
						</tbody>
					</table>
					<td>
					<td>
						<table>
							<tbody>
							<tr>
								<td><?php echo Lang('ingot'); ?></td>
								<td><span class="orangetitle"><?php echo $data['info']['ingot'] ? $data['info']['ingot'] : '-' ?></span></td>
							</tr>
							<tr>
								<td><?php echo Lang('to_this_level_charge_ingot'); ?></td>
								<td><?php echo $data['info']['ingot_vip'] ? $data['info']['ingot_vip'] : '-' ?></td>
							</tr>
							<tr>
								<td><?php echo Lang('charge_ingot_total'); ?></td>
								<td><?php echo $data['info']['charge_ingot'] ? $data['info']['charge_ingot'] : '-' ?></td>
							</tr>
							<tr>
								<td><?php echo Lang('over_recharge_ingot'); ?></td>
								<td><?php echo $data['info']['total_ingot'] ? $data['info']['total_ingot'] : '-' ?></td>
							</tr>
							<tr>
								<td><?php echo Lang('升级到当前级别时间'); ?></td>
								<td><?php echo $data['info']['level_up_time'] ? date('Y-m-d H:i:s', $data['info']['level_up_time']) : '-' ?></td>
							</tr>
							</tbody>
						</table>
					</td>
					<td>
						<table>
							<tbody>
							<tr>
								<td><?php echo Lang('coins'); ?></td>
								<td><?php echo $data['info']['coins']?></td>
							</tr>
							<tr>
								<td><?php echo Lang('prestige'); ?></td>
								<td><?php echo $data['info']['fame']?> (Lv.<?php echo $data['info']['fame_level']?>)</td>
							</tr>
							<tr>
								<td><?php echo Lang('skill'); ?></td>
								<td><?php echo $data['info']['skill']?></td>
							</tr>
							<tr>
								<td><?php echo Lang('player_state_point'); ?></td>
								<td><?php echo $data['info']['state_point'] ? $data['info']['state_point'] : '-' ?></td>
							</tr>
							<tr>
								<td><?php echo Lang('武魂'); ?></td>
								<td>-</td>
							</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<td>
					<table>
						<tbody>
						<tr>
							<td><?php echo Lang('power'); ?></td>
							<td><?php echo $data['info']['power'] ? $data['info']['power'] : '-' ?></td>
						</tr>
						<tr>
							<td><?php echo Lang('other_power'); ?></td>
							<td><?php echo $data['info']['extra_power'] ? $data['info']['extra_power'] : '-' ?></td>
						</tr>
						<tr>
							<td><?php echo Lang('today_pay_power'); ?></td>
							<td><?php echo $data['info']['buy_power_times_today'] ? $data['info']['buy_power_times_today'] : '-' ?></td>
						</tr>
						<tr>
							<td><?php echo Lang('default_deploy_mode'); ?></td>
							<td><?php echo $data['deploy']['deploy_mode_id'] ?></td>
						</tr>
						<tr>
							<td><?php echo Lang('桃树等级'); ?></td>
							<td><?php echo $data['info']['peach_lv'] ? $data['info']['peach_lv'] : '-' ?></td>
						</tr>
						</tbody>
					</table>
					<td>
					<td>
						<table>
							<tbody>
							<tr>
								<td><?php echo Lang('十二宫等级'); ?></td>
								<td><?php echo $data['info']['zodiac_level'] ? $data['info']['zodiac_level'] : '-' ?></td>
							</tr>
							<tr>
								<td><?php echo Lang('十二宫关卡'); ?></td>
								<td><?php echo $data['info']['barrier'] ? $data['info']['barrier'] : '-' ?></td>
							</tr>
							<tr>
								<td><?php echo Lang('last_get_faction_salary_date'); ?></td>
								<td><?php echo date('Y-m-d H:i:s', $data['info']['get_faction_salary_last_date']) ?></td>
							</tr>
							<tr>
								<td><?php echo Lang('travel_event_times'); ?></td>
								<td><?php echo $data['info']['travel_event_join_count'] ? $data['info']['travel_event_join_count'] : '-' ?></td>
							</tr>
							<tr>
								<td><?php echo Lang('last_travel_event_time'); ?></td>
								<td><?php echo date('Y-m-d H:i:s', $data['info']['travel_event_last_time']) ?></td>
							</tr>
							</tbody>
						</table>
					</td>
					<td>
						<table>
							<tbody>
							<tr>
								<td><?php echo Lang('last_refresh_lucky_shop_time'); ?></td>
								<td><?php echo date('Y-m-d H:i:s', $data['info']['last_refresh_lucky_shop_time']) ?></td>
							</tr>
							<tr>
								<td><?php echo Lang('is_auto_rune'); ?></td>
								<td><?php echo $data['info']['is_auto_rune']>0 ? 'YES' : 'NO' ?></td>
							</tr>
							<tr>
								<td><?php echo Lang('last_increase_time'); ?></td>
								<td><?php echo date('Y-m-d H:i:s', $data['info']['last_increase_time']) ?></td>
							</tr>
							<tr>
								<td><?php echo Lang('爬塔最高道数'); ?></td>
								<td><?php echo $data['info']['travel_event_join_count'] ?></td>
							</tr>
							<tr>
								<td><?php echo Lang('爬塔最高层数'); ?></td>
								<td><?php echo date('Y-m-d H:i:s', $data['info']['travel_event_last_time']) ?></td>
							</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
			</table>
		</div>
	</div>
	<div class="column cf" id="table_column_1" style="display:none">
		<div>
		<table>
		<thead>
		<tr>
	    	<th>&nbsp;</th>
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
		</tr>
		</thead>
		<tbody id="partnerlist">
		<?php foreach ($data['partner'] as $key => $value) { ?>
		<tr>
			<td class="num"><?php echo $value['id'] ?></td>
			<td>
			<p style="float:left;">
			<a href="javascript:;" data-key="item" data-id="<?php echo $value['id'] ?>" class="partner" title="查看装备"><?php echo $data['role'][$value['role_id']] ?></a>  Lv.<?php echo $value['level'] ?>
			<?php if ($value['trans_player_role_id'] > 0): ?>
				<br><span class="bluetitle">传承给：<?php echo $data['player'][$value['trans_player_role_id']]?></span>
			<?php endif ?>
			<?php if ($value['be_trans_player_role_id'] > 0): ?>
				<br><span class="bluetitle">被传承：<?php echo $data['player'][$value['be_trans_player_role_id']]?></span>
			<?php endif ?>
			</p>
			<p style="float:right;">

			<?php if ($value['follow_role_id'] == $value['id']): ?>
			<span class="bluetitle">跟随</span><br>
			<?php else: ?>
				<?php if ($value['deploy_mode_id'] > 0): ?>
					上阵<br>
				<?php endif ?>
			<?php endif ?>
			<?php echo $value['state'] == 0 ? '正常' : '<span class="graytitle">离队</span>' ?>
			</p>
			</td>
			<td><?php echo $value['experience'] ?></td>
			<td>
			<span class="redtitle"><?php echo $value['health'] ?></span><br>
			<span class="greentitle"><span class="greentitle"><?php echo $value['max_health'] ?></span></td>
			<td>
			<span class="redtitle"><?php echo $value['attack'] ?></span><br>
			<span class="greentitle"><?php echo $value['defense'] ?></span></td>
			<td>
			<span class="redtitle"><?php echo $value['magic_attack'] ?></span>
			<br>
			<span class="greentitle"><?php echo $value['magic_defense'] ?></span></td>
			<td>
			<span class="redtitle"><?php echo $value['stunt_attack'] ?></span>
			<br>
			<span class="greentitle"><?php echo $value['stunt_defense'] ?></span></td>
			<td>
			<?php echo $value['critical'] ?>/<?php echo $value['dodge'] ?>/<?php echo $value['hit'] ?>/<?php echo $value['block'] ?>
			<br><?php echo $value['break_block'] ?>/<?php echo $value['break_critical'] ?>/<?php echo $value['kill'] ?>
			</td>
			<td><?php echo $value['strength'] ?> 
			<br>
			<span class="greentitle">+<?php echo $value['strength_additional'] ?></span> <span class="greentitle">+<?php echo $value['mission_strength'] ?></span></td>
			<td><?php echo $value['agile'] ?>
			<br>
			<span class="greentitle">+<?php echo $value['agile_additional'] ?></span> <span class="greentitle">+<?php echo $value['mission_agile'] ?></span></td>
			<td><?php echo $value['intellect'] ?>
			<br>
			<span class="greentitle">+<?php echo $value['intellect_additional'] ?></span> <span class="greentitle">+<?php echo $value['mission_intellect'] ?></span></td>
			<td><?php echo !empty($value['spirit_name']) ? $value['spirit_name'].' Lv.'.$value['spirit_lv'] : '--' ?>
			</td>
			<td><a href="javascript:;" data-key="fate" data-id="${id}" class="partner" title="查看命格">命格<?php echo $value['can_wear_fate_number'] ?></a></td>
			<td><a href="javascript:;" data-key="role_elixir" data-id="<?php echo $value['id'] ?>" class="partner" title="查看丹药记录">丹药记录</a></td>
		</tr>
		<?php } ?>
		</tbody>
		</table>
		</div>
	</div>


    <div class="column cf" id="table_column_2" style="display:none">
	<div class="toolbar cf">
		<div class="tool_date cf">
        <div class="control">
        	<div class="frm_cont">
				<ul>
					<li name="condition" id="blockinfolist">
						<?php foreach ($infolist as $key => $value): ?>
							<a href="javascript:;" class="block_info" data-key="<?php echo $value['key'] ?>"><?php echo $value['bname'] ?></a>
						<?php endforeach ?>
					</li>
				</ul>
			</div>
		</div>
		</div>
    </div>
  	<div id="infoarea">

    </div>
	</div>

    <div class="column cf" id="table_column_3" style="display:none">
	<div class="toolbar cf">
		<div class="tool_date cf">
        <div class="control">
        	<div class="frm_cont">
				<ul>
					<li name="condition" id="blockloglist">
						<?php foreach ($loglist as $key => $value): ?>
							<a href="javascript:;" class="block_record" data-key="<?php echo $value['key'] ?>"><?php echo $value['bname'] ?></a>
						<?php endforeach ?>
					</li>
				</ul>
			</div>
		</div>
		</div>
    </div>
    <!--<div class="speed_result">
        <div class="mod_tab_cont">
            <div class="stime">
                <ul>
                    <li>总获得：<em><strong id="total_get">0</strong></em></li>
                    <li>总消耗：<em><strong id="total_con">0</strong></em></li>
                </ul>
            </div>
   		</div>
   	</div>-->
    <div id="logarea">

    </div>
	</div>

	<div class="column cf" id="table_column_4" style="display:none">
		<div class="control">
	        <dl class="dl_list cf">
	        	<dt>修改昵称：</dt>
	        	<dd><label><input type="text" id="ipt_nickname" name="nickname" class="ipt_txt" value="<?php echo $data['info']['nickname'] ?>"></label></dd>
	        </dl>
	        <dl class="dl_list cf">
	        	<dt>&nbsp;</dt><dd>
	        	<label><input type="button" class="btn_sbm" id="ajax-edit-nickname" value="提交修改"></label></dd>
	        </dl>
	    </div>
	</div>