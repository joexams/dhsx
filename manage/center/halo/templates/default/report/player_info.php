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
            var queryData = 'sid=<?php echo $data['sid'] ?>&id=<?php echo $data['id']; ?>&version=<?php echo $data['version']; ?>&key='+key+'&title='+title+'&nickname=<?php echo $data['info']['nickname'] ?>&playername=<?php echo $data['info']['username']; ?>';
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
					Ha.notify.show('<?php echo Lang('not_find_player_record');?>', '', 'error');
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
			Ha.notify.show('<?php echo Lang('error_nickname_nochange_or_empty');?>');
		}
	});
});
</script>

<h2><span id="tt"><?php echo Lang('player_detail');?>：
<a href="#app=4&cpp=35&url=<?php echo urlencode(WEB_URL.INDEX.'?m=report&c=player&v=init'); ?>"><?php echo Lang('game_server') ?></a>
<span>&gt;</span><a href="#app=4&cpp=35&url=<?php echo urlencode(WEB_URL.INDEX.'?m=report&c=player&v=player_list&sid='.$data['sid'].'&title='.$data['title'])?>"><?php echo $data['title']; ?></a>
</span></h2>
<div class="container" id="container">
	<div class="toolbar">
		<div class="tool_date">
			<div class="title cf">
				<form id="get_search_submit" method="get" name="form">
				<div class="tool_group">
					 <label><?php echo Lang('player');?>：
						<input type="text" name="username" id="player_name" value="" class="ipt_txt"></label>
					<input type="hidden" name="dogetSubmit" value="1">
					<input type="submit" class="btn_sbm" value="<?php echo Lang('changing_over');?>">
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
			    	<span class="thin1"><?php echo Lang('player');?>ID：</span><label><?php echo $data['info']['id']; ?></label>
			    	<span class="thin1"><?php echo Lang('player_name');?>：</span><?php echo $data['info']['username']; ?></label>
			    	<span class="thin1"><?php echo Lang('player_nick');?>：</span><?php echo $data['info']['nickname']; ?></label>
			    	<?php if (!$limit) { ?>
			    	<label id="tester">
			    		<?php if ($data['info']['is_tester'] == 1) {  ?>
			    		<a class="redtitle"><?php echo Lang('tester');?></a>
			    		<?php }elseif ($data['info']['is_tester'] == 2) { ?>
			    		<a class="redtitle"><?php echo Lang('senior_tester');?></a>
			    		<?php }elseif ($data['info']['is_tester'] == 3) { ?>
			    		<a class="redtitle">GM</a>
			    		<?php }elseif ($data['info']['is_tester'] == 4) { ?>
			    		<a class="redtitle"><?php echo Lang('newer_guide');?></a>
			    		<?php } ?>
			    	</label>
			    	<?php } ?>
			    	<span class="thin1"><?php echo Lang('register');?>：<?php echo date('Y-m-d H:i:s', $data['info']['first_login_time'])?>/<?php echo $data['info']['first_login_ip'] ?></span>
			    	<span class="thin1"><?php echo Lang('last_login_time');?>：<?php echo date('Y-m-d H:i:s', $data['info']['last_login_time'])?>/<?php echo $data['info']['last_login_ip'] ?></span>
			    </li>
				</ul>
			</div>
		</div>
	    <div class="mod_tab_title first_level_tab">
	    	<ul>
	    		<?php foreach ($playlist_menu as $key1 => $value1){?>
	    			<li <?php if ($key1==0){?> class="current" <?php }?>><a href="javascript:void(0);" data-type="<?php echo $value1['data'];?>" class="playertype"><?php echo $value1['mname'];?></a></li>
	    		<?php }?>
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
							<td style="width:200px"><?php echo Lang('level'); ?>/vip<?php echo Lang('level'); ?></td>
							<td><strong><?php echo $data['info']['level'] ?>/<?php echo $data['info']['vip_level'] ?></strong></td>
						</tr>
						<tr>
								<td><?php echo Lang('ingot'); ?></td>
								<td><span class="orangetitle"><?php echo $data['info']['ingot'] ? $data['info']['ingot'] : '-' ?></span></td>
							</tr>
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
						
						</tbody>
					</table>
					<td>
					<td>
						<table>
							<tbody>
							<tr>
								<td style="width:200px"><?php echo Lang('attack_power'); ?></td>
								<td><?php echo $data['info']['attack_power'] ? $data['info']['attack_power'] : '-' ?></td>
							</tr>
							<tr>
							<td><?php echo Lang('unique_skill'); ?></td>
							<td><?php echo $data['role_stunt']['role_stunt_name'] ? $data['role_stunt']['role_stunt_name'] : '-' ?></td>
						</tr>
							
							<tr>
								<td><?php echo Lang('passivity'); ?></td>
								<td><?php echo $data['passivity_stunt']['passivity_stunt_name'] ? $data['passivity_stunt']['passivity_stunt_name'] : '-' ?></td>
							</tr>
							<tr>
							<td><?php echo Lang('default_deploy_mode'); ?></td>
							<td><?php echo $data['deploy'][$data['info']['deploy_mode_id']] ?></td>
						</tr>
							<tr>
								<td><?php echo Lang('update_level_time'); ?></td>
								<td><?php echo $data['info']['level_up_time'] ? date('Y-m-d H:i:s', $data['info']['level_up_time']) : '-' ?></td>
							</tr>
							</tbody>
						</table>
					</td>
					<td>
						<table>
							<tbody>
							<tr>
							<td style="width:200px"><?php echo Lang('marry_level'); ?></td>
							<td><?php echo $data['marry_info']['favor_value'] ? intval($data['marry_info']['favor_value']/1000).'级' : '-' ?></td>
						</tr>
							<tr>
							<td><?php echo Lang('peach_tree_level'); ?></td>
							<td><?php echo $data['info']['peach_lv'] ? $data['info']['peach_lv'] : '-' ?></td>
						</tr>
						<tr>
								<td><?php echo Lang('dragonball_level'); ?></td>
								<td><?php echo $data['info']['stage'] ? $data['info']['stage'].'阶'.$data['pet_animal']['pet_animal_name'] : '-' ?></td>
							</tr>
							<tr>
								<td><?php echo Lang('mission_process_level'); ?></td>
								<td><?php echo $data['mission'][$data['info']['max_mission_lock']] ? $data['mission'][$data['info']['max_mission_lock']] : '-' ?></td>
							</tr>
							<tr>
							<td><?php echo Lang('hero_mission_process_level'); ?></td>
							<td><?php echo $data['mission'][$data['info']['max_hero_mission_lock']] ? $data['mission'][$data['info']['max_hero_mission_lock']] : '-' ?></td>
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
							<td style="width:200px"><?php echo Lang('feats'); ?></td>
							<td><?php echo $data['info']['feats_lv'] ? Lang('peakedness').intval($data['info']['feats_lv']/2000).' '.$data['info']['feats'].'('.$data['info']['feats_lv'].'/'.((intval($data['info']['feats_lv']/2000)+1)*2000).')' : '-' ?></td>
						</tr>
						<tr>
								<td><?php echo Lang('player_state_point'); ?></td>
								<td><?php echo $data['info']['state_point'] ? $data['info']['state_point'] : '-' ?></td>
							</tr>
						<tr>
							<td><?php echo Lang('spirit_name'); ?></td>
							<td><?php echo $data['info']['item_spirit'] ? $data['info']['item_spirit'] : '-' ?></td>
						</tr>
						<tr>
								<td><?php echo Lang('chaotic_ichor'); ?></td>
								<td><?php echo $data['info']['chaotic_ichor'] ? $data['info']['chaotic_ichor'] : '-' ?></td>
							</tr>
						
						<tr>
							<td><?php echo Lang('power'); ?></td>
							<td><?php echo $data['info']['power'] ? $data['info']['power'] : '-' ?></td>
						</tr>
						</tbody>
					</table>
					<td>
					<td>
						<table>
							<tbody>
							<tr>
							<td style="width:200px"><?php echo Lang('sport_ranking'); ?></td>
							<td><?php echo $data['info']['ranking'] ? $data['info']['ranking'] : '-' ?></td>
						</tr>
							<tr>
								<td><?php echo Lang('faction'); ?></td>
								<td><?php echo $data['faction']['faction_name'] ? $data['faction']['faction_name'] : '-' ?></td>
							</tr>
							<tr>
								<td><?php echo Lang('last_increase_time'); ?></td>
								<td><?php echo $data['info']['last_increase_time'] ? date('Y-m-d H:i:s', $data['info']['last_increase_time']) : '-' ?></td>
							</tr>
							<tr>
								<td><?php echo Lang('is_auto_rune'); ?></td>
								<td><?php echo $data['info']['is_auto_rune']>0 ? 'YES' : 'NO' ?></td>
							</tr>
							<tr>
								<td>-</td>
								<td>-</td>
							</tr>
							</tbody>
						</table>
					</td>
					<td>
						<table>
							<tbody>
							<tr>
								<td style="width:200px"><?php echo Lang('travel_progress'); ?></td>
								<td><?php echo $tower_info ? $tower_info : '-' ?></td>
							</tr>
							<tr>
								<td><?php echo Lang('nine_regions_progress'); ?></td>
								<td><?php echo $nine_regions["name"] ? $nine_regions["name"].'第'.($nine_regions["level_id"]+1).'关' : '-' ?></td>
							</tr>
							<tr>
								<td><?php echo Lang('zodiac_progress'); ?></td>
								<td><?php echo $data['zodiac_info']['name'] ? $data['zodiac_info']['name'].'第'.$data['info']["barrier"].'层' : '-' ?></td>
							</tr>
							<tr>
								<td><?php echo Lang('baxian_progress'); ?></td>
								<td><?php echo $data['baxian_info']["name"] ? $data['info']["baxian_level"].'轮渡海'.$data['baxian_info']["name"] : '-' ?></td>
							</tr>
							<tr>
								<td>-</td>
								<td>-</td>
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
	    	<th><?php echo Lang('partner').Lang('name'); ?></th>
	    	<th><?php echo Lang('experience'); ?></th>
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
			<td class="num"><?php if (!$limit){ echo $value['id'];} ?></td>
			<td>
			<p style="float:left;">
			<a href="javascript:;" data-key="item" data-id="<?php echo $value['id'] ?>" class="partner" title="查看装备"><?php echo $value['role_name'] ?></a>  Lv.<?php echo $value['level'] ?>
			<?php if ($value['trans_player_role_id'] > 0): ?>
				<br><span class="bluetitle">传承给：<?php echo $data['partner'][$value['trans_player_role_id']]['role_name'] ?></span>
			<?php endif ?>
			<?php if ($value['be_trans_player_role_id'] > 0): ?>
				<br><span class="bluetitle">被传承：<?php echo $data['partner'][$value['be_trans_player_role_id']]['role_name'] ?></span>
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
			<td><a href="javascript:;" data-key="fate" data-id="<?php echo $value['id'] ?>" class="partner" title="查看命格">命格<?php echo $value['can_wear_fate_number'] ?></a></td>
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
							<a href="javascript:;" class="block_info" data-key="<?php  $dataarr = explode("=",$value['data']); echo $dataarr[1];?>"><?php echo $value['mname'] ?></a>
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
							<a href="javascript:;" class="block_record" data-key="<?php  $dataarr = explode("=",$value['data']); echo $dataarr[1]?$dataarr[1]:'pay'; ?>"><?php echo $value['mname'] ?></a>
						<?php endforeach ?>
					</li>
				</ul>
			</div>
		</div>
		</div>
    </div>
    <!--<div class="speed_result" id="total_get_con" style="display:none">
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
	
	<div class="column cf" id="table_column_5" style="display:none">
		<form action="<?php echo FAST_LOGIN;?>" method="post">
		<input type="hidden" name="username" value="<?php echo $data['info']['username']; ?>">
		<input type="hidden" name="password" value="xd9527">
		<input type="hidden" name="invite_user" value="">
		<input type="hidden" name="api_url" value="<?php echo $server['server']; ?>">
		<input type="hidden" name="server_ver" value="<?php echo $server['server_ver']; ?>">
		<div class="control">
			<dl class="dl_list cf">
	        	<dt>是否黄钻：</dt>
	        	<dd><label><select class="ipt_select" name="is_yellow_vip"><option value="1">是</option><option value = "0" selected>否</option></select></label></dd>
	        </dl>
	        <dl class="dl_list cf">
	        	<dt>是否年费黄钻：</dt>
	        	<dd><label><select class="ipt_select" name="is_yellow_year_vip"><option value="1">是</option><option value = "0" selected>否</option></select></label></dd>
	        </dl>
	        <dl class="dl_list cf">
	        	<dt>黄钻等级：</dt>
	        	<dd><label><input type="text" name="yellow_vip_level" class="ipt_txt" value="0"></label></dd>
	        </dl>
	        <dl class="dl_list cf">
	        	<dt>是否蓝钻：</dt>
	        	<dd><label><select class="ipt_select" name="is_blue_vip"><option value="1">是</option><option value = "0" selected>否</option></select></label></dd>
	        </dl>
	        <dl class="dl_list cf">
	        	<dt>是否年费蓝钻：</dt>
	        	<dd><label><select class="ipt_select" name="is_blue_year_vip"><option value="1">是</option><option value = "0" selected>否</option></select></label></dd>
	        </dl>
	        <dl class="dl_list cf">
	        	<dt>是否豪华蓝钻：</dt>
	        	<dd><label><select class="ipt_select" name="is_super_blue_vip"><option value="1">是</option><option value = "0" selected>否</option></select></label></dd>
	        </dl>
	        <dl class="dl_list cf">
	        	<dt>蓝钻等级：</dt>
	        	<dd><label><input type="text" name="blue_vip_level" class="ipt_txt" value="0"></label></dd>
	        </dl>
	        <dl class="dl_list cf">
	        	<dt>3366等级：</dt>
	        	<dd><label><input type="text" name="3366_level" class="ipt_txt" value="0"></label></dd>
	        </dl>
	        <dl class="dl_list cf">
	        	<dt>qq等级：</dt>
	        	<dd><label><input type="text" name="qq_level" class="ipt_txt" value="0"></label></dd>
	        </dl>
	        <dl class="dl_list cf">
	        	<dt>qq会员等级：</dt>
	        	<dd><label><input type="text" name="qq_vip_level" class="ipt_txt" value="0"></label></dd>
	        </dl>
	        <dl class="dl_list cf">
	        	<dt>是否年费qq会员：</dt>
	        	<dd><label><input type="text" name="is_year_vip" class="ipt_txt" value="0"></label></dd>
	        </dl>
	        <dl class="dl_list cf">
	        	<dt>q+等级：</dt>
	        	<dd><label><input type="text" name="qplus_level" class="ipt_txt" value="0"></label></dd>
	        </dl>
	        <dl class="dl_list cf">
	        	<dt>来源：</dt>
	        	<dd><label><select class="ipt_select" name="plat_form">
	        		<option value="1">qq空间</option>
                    <option value="2">朋友</option>
                    <option value="3">qq微博</option>
                    <option value="4">qq+</option>
                    <option value="5">财付通</option>
                    <option value="6">qqgame</option>
                    <option value="7">website</option>
                    <option value="8">3366</option>
                    <option value="9">游戏联盟</option>
                    <option value="10">赤兔</option>	        		
	        	</select></label></dd>
	        </dl>
	        <dl class="dl_list cf">
	        	<dt>心悦vip类型：</dt>
	        	<dd><label><input type="text" name="xinyue_vip_type" class="ipt_txt" value="0"></label></dd>
	        </dl>
	        <dl class="dl_list cf">
	        	<dt>心悦vip等级：</dt>
	        	<dd><label><input type="text" name="xinyue_vip_level" class="ipt_txt" value="0"></label></dd>
	        </dl>
	        <dl class="dl_list cf">
	        	<dt>&nbsp;</dt><dd>
	        	<label><input type="submit" name="login" class="btn_sbm" value="飞升"></label></dd>
	        </dl>
	    </div>
	    </form>
	</div>