<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
Ha.page.recordNum = 0;
Ha.page.pageCount = 0;
Ha.page.pageSize = 20;
Ha.page.listEid = 'playerlist';
Ha.page.colspan = 8;
Ha.page.emptyMsg = '没有找到玩家数据。';
Ha.page.url = "<?php echo INDEX; ?>?m=server&c=get&v=public_player_list";
Ha.page.queryData = {sid: <?php echo $data['sid'] ?>, cid: <?php echo $data['cid'] ?>, order: ''};

var playerlist;
var dialog = typeof dialog != undefined ? null : '';
$(function(){
	Ha.page.getList(1, function(data){
		playerlist = data.list;
	});

	$('#selectOrder').on('click', 'a.order', function(){
		$('.active', $('#selectOrder')).removeClass('active');
		var order = $(this).attr('data');
		$(this).addClass('active');

		Ha.page.recordNum = 0;
		Ha.page.queryData = $('#get_search_submit').serialize()+'&order='+$(this).attr('data');
		Ha.page.getList(1);

		if (order.indexOf('mission') >= 0) {
			if ($('#playerlistheader').find('.add').is('th') == false) {
				$('#playerlistheader').find('th').eq(5).after('<th class="add">关卡进度</th>');
			}
		}else {
			$('#playerlistheader').find('th.add').remove();
		}
	});

	$('#get_search_submit').on('submit', function(e){
		e.preventDefault();
		Ha.page.recordNum = 0;
		Ha.page.queryData = $('#get_search_submit').serialize();
		Ha.page.getList(1);
	});

//	$('#query').on('click', function(e){
//		e.preventDefault();
//		Ha.page.recordNum = 0;
//		Ha.page.queryData = $('#get_search_submit').serialize();
//		Ha.page.getList(1);
//	});

	$('#moreQuery').on('click', function(e){
		$('#moreConditions').toggle();
	});

	/**
	 * 上一页、下一页
	 * @return {[type]} [description]
	 */
	$('#singlepage').on('click', 'a', function(){
		var pflag = $(this).attr('class');
		if (pflag == 'next'){
			if ((pageIndex+1) <= pageCount){
				Player.getList(pageIndex+1);
			}
		}else if (pflag == 'prev'){
			if ((pageIndex-1) > 0){
				Player.getList(pageIndex-1);
			}
		}
	});
});
</script>

<script type="text/template" id="playerlisttpl">
<tr {{if nickname != ''}}data-id="${id}"{{else}}title="未创建角色"{{/if}}>
<td class="num"><?php if (!$limit) { ?>${id}<?php } ?>{{if name != ''}}<?php if (!$limit) { ?><br><?php } ?>${name}{{else}}&nbsp;{{/if}}</td>
<td>
{{if nickname != ''}}
<a href="#app=4&cpp=35&url=${encodeurl('report', 'player_info', 'player', '&sid=<?php echo $data['sid'] ?>&id=')}${id}" data-id="${id}" data-username="${username}" class="userinfo">${username}<br><span class="bluetitle">${nickname}</span></a>
{{else}}
${username}
{{/if}}

{{if vip_level > 0}}
<img class="grade lvl_img" src="<?php echo WEB_URL; ?>static/images/V${vip_level}.png">
{{/if}}

{{if is_yellow_year_vip == 1}}
<img class="grade lvl_img" src="<?php echo WEB_URL; ?>static/images/qz_vip_icon_fla_l_year_${yellow_vip_level}.png">
{{else}}
	{{if is_yellow_vip == 1 && yellow_vip_level > 0}}<span class="qz_vip_icon_s_${yellow_vip_level}">&nbsp;</span>{{/if}}
{{/if}}
{{if is_blue_year_vip == 1}}
<img style="" class="year nianicon" src="static/images/year_icon.png">
{{/if}}
{{if blue_vip_level > 0}}
<img class="grade lvl_img" src="static/images/lz_a_on_${blue_vip_level}.png">
{{/if}}
<?php if (!$limit) { ?>
{{if is_tester == 1}}
	 <span class="redtitle"><?php echo Lang('tester'); ?></span>
{{else is_tester == 2}}
	 <span class="redtitle"><?php echo Lang('senior_tester'); ?></span>
{{else is_tester == 3}}
	 <span class="redtitle">GM</span>
{{else is_tester == 4}}
 	 <span class="redtitle"><?php echo Lang('newer_guide'); ?></span>
{{/if}}
<?php } ?>
<br>
{{if last_offline_time > 0}}${date('Y-m-d H:i', last_offline_time)}<br>{{/if}}
{{if source != ''}}FROM: ${source}{{/if}}
</td>
<td>{{if level > 0}}${level}{{else}}--{{/if}}</td>
<td>
<strong class="greentitle">${sum(charge_ingot,ingot)}</strong>
{{if ingot > 0}}<br><span class="graytitle">赠：${ingot}</span>{{/if}}
{{if charge_ingot > 0}}<br><span class="orangetitle">充：${charge_ingot}</span>{{/if}}
</td>
<td><strong class="redtitle">{{if total_ingot > 0}}${total_ingot}{{else}}0{{/if}}</strong></td>
<td>${coins}</td>
{{if typeof mission_name != 'undefined'}} 
<td>${mission_name}<br>${date('Y-m-d H:i', first_challenge_time)}</td>
{{/if}}
<td>${last_login_ip}<br>
${date('Y-m-d H:i', last_login_time)}</td>
</tr>
</script>

<!-- <div id="tipsBar" class="msg_tips"> 
    <i class="i_tips"></i>
    <p id="p_tips">玩家记录为灰色且不可点击则为“未创建角色”</p>
</div> -->
<h2><span id="tt"><?php echo Lang('玩家数据'); ?>：
	<a href="#app=4&cpp=35&url=<?php echo urlencode(WEB_URL.INDEX.'?m=report&c=player&v=init') ?>"><?php echo Lang('game_server') ?></a>
	<span>&gt;</span><?php echo $data['title']; ?>
	<span>&gt;</span><?php echo Lang('player_list') ?>
</span></h2>
<div class="container" id="container">
	    <?php include template('report', 'player_top'); ?>

	<div class="toolbar">
		<div class="tool_date cf">
			<form id="get_search_submit" action="" method="get" name="form">
			<div class="title cf">	
				<div class="tool_group">
					<label><?php echo Lang('player'); ?>：<input type="text" class="ipt_txt_l" name="username"></label>
					<input name="cid" type="hidden" value="<?php echo $data['cid'] ?>">
					<input name="sid" type="hidden" value="<?php echo $data['sid'] ?>">
					<input name="dogetSubmit" type="hidden" value="1">
					<input type="submit" class="btn_sbm" value="查询" id="query"> 
					<input type="reset" class="btn_rst" value="重置" id="reset">
				</div>
				<div class="more">
					<a href="javascript:;" id="moreQuery"><i class="i_triangle"></i>高级查询</a>
				</div>
			</div>
			<div class="control cf" id="moreConditions" style="display: ;">
				<div class="frm_cont">
					<ul>
						<li name="condition">
							<label class="frm_info">附加条件：</label>
							<select name="vip" class="ipt_select" style="width:100px;">
								 <option value="">VIP范围</option>
								 <option value="1">VIP1 以上</option>
								 <option value="2">VIP2 以上</option>
								 <option value="3">VIP3 以上</option>
								 <option value="4">VIP4 以上</option>
								 <option value="5">VIP5 以上</option>
								 <option value="6">VIP6 以上</option>
								 <option value="7">VIP7 以上</option>
								 <option value="8">VIP8 以上</option>
								 <option value="9">VIP9 以上</option>
								 <option value="10">VIP10 以上</option>
								 <option value="11">VIP11 以上</option>
								 <option value="12">VIP12 以上</option>
							</select>
							<?php if (!$limit) { ?>
							<select name="is_tester" class="ipt_select" style="width:100px;">
							 <option value=""><?php echo Lang('type'); ?></option>
							 <option value="1,2"><?php echo Lang('tester'); ?></option>
							 <option value="4"><?php echo Lang('newer_guide'); ?></option>
							 <option value="3">GM</option>
							</select>
							<?php } ?>
							<select name="is_online" class="ipt_select" style="width:100px;">
								 <option value="">是否在线</option>
								 <option value="2">是</option>
								 <option value="1">否</option>
							</select>

							<?php echo Lang('between_level'); ?>：	
							<input name="minlevel" type="text" value="0" class="ipt_txt_s"> ~	
							<input name="maxlevel" type="text" value="0" class="ipt_txt_s">
							<?php echo Lang('channel'); ?>：
							<input type="text" name="source" value="" class="ipt_txt_s">

							<span style="width:30px;">IP号：</span>
							<input name="ip" type="text" value="" maxlength="20" class="ipt_txt_s"> 
						</li>
						<li name="condition">
							<label class="frm_info">QQ属性：</label>
							<select name="yellow" class="ipt_select" style="width:100px;">
							 <option value=""><?php echo Lang('is_yellow_vip'); ?></option>
							 <option value="1"><?php echo Lang('yellow_vip'); ?></option>
							 <option value="2"><?php echo Lang('year_yellow_vip'); ?></option>
							</select>
							<select name="yellow_level" class="ipt_select" style="width:100px;">
							 <option value=""><?php echo Lang('yellow_vip_level'); ?></option>
							 <option value="1">1级</option>
							 <option value="2">2级</option>
							 <option value="3">3级</option>
							 <option value="4">4级</option>
							 <option value="5">5级</option>
							 <option value="6">6级</option>
							 <option value="7">7级</option>
							 <option value="8">8级</option>
							</select>	
							<select name="blue" class="ipt_select" style="width:100px;">
							 <option value=""><?php echo Lang('is_blue_vip'); ?></option>
							 <option value="1"><?php echo Lang('blue_vip'); ?></option>
							 <option value="2"><?php echo Lang('year_blue_vip'); ?></option>
							</select>	
							<select name="blue_level" class="ipt_select" style="width:100px;">
							 <option value=""><?php echo Lang('blue_vip_level'); ?></option>
							 <option value="1">1级</option>
							 <option value="2">2级</option>
							 <option value="3">3级</option>
							 <option value="4">4级</option>
							 <option value="5">5级</option>
							 <option value="6">6级</option>
							 <option value="7">7级</option>
							 <option value="8">8级</option>
							</select>
						</li>
						<li name="condition" id="selectOrder">
							<label class="frm_info">排序：</label>
							<a href="javascript:;" class="order active" data=""><?php echo Lang('all') ?></a>
							<a href="javascript:;" class="order" data="level"><?php echo Lang('level') ?></a>
							<a href="javascript:;" class="order" data="vip">VIP</a>
							<a href="javascript:;" class="order" data="ingot"><?php echo Lang('ingot') ?></a>
							<a href="javascript:;" class="order" data="level"><?php echo Lang('consumption') ?></a>
							<a href="javascript:;" class="order" data="coins"><?php echo Lang('coins') ?></a>
							<a href="javascript:;" class="order" data="fame"><?php echo Lang('skill') ?></a>
							<a href="javascript:;" class="order" data="mission"><?php echo Lang('ord_replica'); ?></a>
							<a href="javascript:;" class="order" data="heromission"><?php echo Lang('hero_replica'); ?></a>
							<a href="javascript:;" class="order" data="wyhmission">按精英万妖皇副本</a>
						</li>
					</ul>
				</div>
			</div>
		</form>
	</div>		
</div>
<div class="column cf" id="table_column">
	<div id="dataTable">
	<table>
	<thead>
	<tr id="playerlistheader">
    	<th>&nbsp;</th>
	    <th><?php echo Lang('player_name') ?>/<?php echo Lang('player_nick') ?></th>
	    <th><?php echo Lang('level') ?></th>
	    <th><?php echo Lang('over_total_ingot') ?></th>
	    <th><?php echo Lang('recharge_ingot') ?></th>
	    <th><?php echo Lang('coins') ?></th>
	    <th><?php echo Lang('last_login_time') ?></th>
	</tr>
	</thead>
	<tbody id="playerlist">
		   
	</tbody>
	</table>
	</div>
	<div id="pageJs" class="page">
        <div id="pager" class="page">
        </div>
	</div>
</div>
</div>