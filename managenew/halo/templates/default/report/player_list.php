<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var Player = {};

var pageIndex = 1, pageCount = 0, pageSize = 20, recordNum = 0, playerlist, order='';
Player.getList = function(index){
	var query = '<?php echo INDEX; ?>?m=server&c=get&v=player_list';
	pageIndex = index;
	$( "#playerlist" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			data: {sid: <?php echo $data['sid'] ?>, cid: <?php echo $data['cid'] ?>, order: order, top: index, recordnum: recordNum},
			success: Player.showList
		});
	});
}
Player.getsearchList = function(index){
	var query = '<?php echo INDEX; ?>?m=server&c=get&v=player_list';
	pageIndex = index;
	$.ajax({
		dataType: "json",
		url: '<?php echo INDEX; ?>?m=server&c=get&v=player_list&order='+order+'&top='+index+'&recordnum='+recordNum,
		data: $('#get_search_submit').serialize(),
		success: function (data){
			Player.showList(data, 1);
		}
	});
}
Player.showList =function ( data, type) {
	if (data.status == -1){
		$('#playerlist').html(data.msg);
	}else {
		recordNum = data.count;
		pageCount = Math.ceil( data.count/pageSize ), playerlist = data.list;
		if (type != undefined && type == 1){
			$( ".pager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: Player.getsearchList });
		}else {
			$( ".pager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: Player.getList });
		}
		$( "#playerlist" ).empty();
		if (data.count > 0){
			$( "#playerlisttpl" ).tmpl( playerlist ).prependTo( "#playerlist" );
			$( "#playerlist" ).stop(true,true).hide().slideDown(400);
			// $("tr").css('cursor', 'pointer');
			if (pageCount > 1){
				$( "#playerlist" ).parent().parent('div.content').css('height', $('#playerlist').parent('table.global').css('height'));
			}
		}
	}
}

function sum(plus1, plus2){
	return parseInt(plus1)+parseInt(plus2);
}

var dialog = typeof dialog != undefined ? null : '';
$(document).ready(function(){
	Player.getList(pageIndex);

	$('.menu_search').on('click', 'a.order', function(){
		order = $(this).attr('data');
		$('.on', $('.menu_search')).removeClass('on');
		$(this).addClass('on');
		$('#get_search_submit').submit();
		if (order.indexOf('mission') >= 0) {
			if ($('#playerlistheader').find('.add').is('th') == false) {
				$('#playerlistheader').find('th').eq(6).after('<th class="add" style="width:120px">关卡进度</th>');
			}
		}else {
			$('#playerlistheader').find('th.add').remove();
		}
	});
	/**
	 * 点击下拉
	 * @return {[type]} [description]
	 */
	$('#playerlist').on('click', 'tr', function(){
		var id = $(this).attr('data-id');
		if (id > 0){
			$('.expend').each(function(){
				$(this).fadeOut(300);
			});
			if ($('#expend_'+id).html() == null){
				for(var key in playerlist){
					if (id == playerlist[key].id){
						$('#expendtpl').tmpl(playerlist[key]).insertAfter($(this));
						break;
					}
				}
			}else {
				if ($('#expend_'+id).is(':hidden')){
					$('#expend_'+id).fadeIn(300);
				}else {
					$('#expend_'+id).fadeOut(300);
				}
			}
		}
	});
	//进入列表
	// $('#playerlist').on('click', 'a.userinfo', function(){
	// 	var id = $(this).attr('data-id'), title = '玩家'+$(this).attr('data-username');
	// 	if (id > 0){
	// 		dialog = $.dialog({id: 'player_info_'+id, width: 880, title: title});
	// 		$.ajax({
	// 			url: '<?php echo INDEX; ?>?m=report&c=player&v=detail_info',
	// 			data: {id: id, sid: "<?php echo $data['sid'] ?>"},
	// 			success: function(data){
	// 				dialog.content(data);
	// 			}
	// 		});
	// 	}
	// });
	
	// $('#playerlist').on('click', 'a.userinfo', function(){
	// 	window.open($(this).attr('href'));
	// 	return false;
	// });

	$('#get_search_submit').on('submit', function(e){
		e.preventDefault();
		recordNum = 0;
		Player.getsearchList(1);
	});
	
	$('tbody[id$="list"] td').live({
		mouseover: function(){
			$('thead th').eq($(this).index()).addClass('ruled');
		},
		mouseout: function(){
			$('thead th').eq($(this).index()).removeClass('ruled');
		}
	});
	
	// $('.dash1').on('click', 'a.othermenu', function() {
	// 	var obj = $(this), url = obj.attr('rel');
	// 	if (url != ''){
	// 		url = url + '&title=<?php echo $data["title"] ?>';
	// 		$('#container').load(url,function(response,status){});
	// 	}
	// });

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

// $(window).scroll(function(){
// 	var value=$(this).scrollTop();
// 	if (value > 200){
// 		$('#playerlistheader').attr('class', 'fixed');
// 	}else {
// 		$('#playerlistheader').attr('class', '');
// 	}
// });
</script>

<script type="text/template" id="expendtpl">
	<tr id="expend_${id}" class="expend">
		<th>&nbsp;</th>
		<th>
		<?php echo Lang('role'); ?>：{{if name != ''}}${name}{{else}}-{{/if}}
		<br><?php echo Lang('skill'); ?>：${skill}
		<br><?php echo Lang('fame'); ?>：${fame}
		</th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		<td colspan="5"><?php echo Lang('over_give_ingot') ?>：<strong>${ingot}</strong>
		    <br>
		    <?php echo Lang('over_recharge_ingot') ?>：<strong>${charge_ingot}</strong></td>
	</tr>
</script>

<script type="text/template" id="playerlisttpl">
<tr {{if nickname != ''}}data-id="${id}"{{/if}}>
<th>${id}</th>
<td>
{{if nickname != ''}}
<a href="#app=5&cpp=24&url=${encodeurl('report', 'detail_info', 'player', '&sid=<?php echo $data['sid'] ?>&sname=<?php echo $data['title'] ?>&id=')}${id}" data-id="${id}" data-username="${username}" class="userinfo">${username}</a> <br><span class="bluetitle">(${nickname})</span>
{{else}}
${username}<br><span class="graytitle">(未创建角色)</span>
{{/if}}
{{if is_yellow_year_vip == 1}}
	 - <span style="color:#F48423">包年黄钻${yellow_vip_level}级</span>
{{else}}
	{{if is_yellow_vip == 1}} - <span style="color:#F48423">黄钻${yellow_vip_level}级</span>{{/if}}
{{/if}}
{{if is_blue_year_vip == 1}}
	 - <span style="color:blue">包年蓝钻${blue_vip_level}级</span>
{{else}}
	{{if is_blue_vip == 1}} - <span style="color:blue">蓝钻${yellow_vip_level}级</span>{{/if}}
{{/if}}
{{if is_tester == 1}}
	 - <span class="redtitle"><?php echo Lang('tester'); ?></span>
{{else is_tester == 2}}
	 - <span class="redtitle"><?php echo Lang('senior_tester'); ?></span>
{{else is_tester == 3}}
	 - <span class="redtitle">GM</span>
{{else is_tester == 4}}
 	 - <span class="redtitle"><?php echo Lang('newer_guide'); ?></span>
{{/if}}
<br>
{{if last_offline_time > 0}}${date('Y-m-d H:i', last_offline_time)}{{/if}}
{{if source != ''}}<br>FROM: ${source}{{/if}}
</td>
<td>{{if level > 0}}${level}{{else}}-{{/if}}</td>
<td><span style="color:green">${vip_level}</span></td>
<td>${sum(charge_ingot,ingot)}</td>
<td>{{if total_ingot > 0}}${total_ingot}{{else}}-{{/if}}</td>
<td>${coins}</td>
{{if typeof mission_name != 'undefined'}} 
<td>${mission_name}<br>${date('Y-m-d H:i', first_challenge_time)}</td>
{{/if}}
<td>${last_login_ip}<br>
${date('Y-m-d H:i', last_login_time)}</td>
<td>&nbsp;</td>
</tr>
</script>

<div id="bgwrap">
	<div class="locationbq">
        <div class="locLeft">
            <a href="#app=5&cpp=24&url=<?php echo urlencode(WEB_URL.INDEX.'?m=report&c=player&v=init') ?>"><?php echo Lang('game_server') ?></a>
            <span>&gt;</span><?php echo $data['title']; ?>
            <span>&gt;</span><?php echo Lang('player_list') ?>
        </div>
        <div class="logo"></div>
    </div>

	<ul class="dash1">
		<li class="fade_hover selected"><a href="<?php echo $url1 ?>" class="othermenu"><span><?php echo Lang('player_list') ?></span></a></li>
		<li class="fade_hover"><a href="<?php echo $url2 ?>" rel="<?php echo INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=faction&sid='.$data['sid'].'&cid='.$data['cid'].'' ?>" class="othermenu"><span><?php echo Lang('faction') ?></span></a></li>
		<li class="fade_hover"><a href="<?php echo $url3 ?>" rel="<?php echo INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=arena&sid='.$data['sid'].'&cid='.$data['cid'].'' ?>" class="othermenu"><span><?php echo Lang('arena') ?></span></a></li>
		<li class="fade_hover"><a href="<?php echo $url4 ?>" rel="<?php echo INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=gamelog&sid='.$data['sid'].'&cid='.$data['cid'].'' ?>" class="othermenu"><span><?php echo Lang('game_log') ?></span></a></li>
		<li class="fade_hover"><a href="<?php echo $url5 ?>" rel="<?php echo INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=gamewar&sid='.$data['sid'].'&cid='.$data['cid'].'' ?>" class="othermenu"><span>群仙会</span></a></li>
	</ul>
	<div class="nav clear">
		<form id="get_search_submit" action="" method="get" name="form">
		<ul class="nav_li">
			<li>
				<p>
					<select name="vip">
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
				</p>
				<p>
					<select name="is_tester">
					 <option value=""><?php echo Lang('type'); ?></option>
					 <option value="1,2"><?php echo Lang('tester'); ?></option>
					 <option value="4"><?php echo Lang('newer_guide'); ?></option>
					 <option value="3">GM</option>
					</select>
				</p>
			</li>
			<li>
				<p>
					<select name="yellow">
					 <option value=""><?php echo Lang('is_yellow_vip'); ?></option>
					 <option value="1"><?php echo Lang('yellow_vip'); ?></option>
					 <option value="2"><?php echo Lang('year_yellow_vip'); ?></option>
					</select>
					<select name="yellow_level">
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
				</p>
				<p>
					<select name="blue">
					 <option value=""><?php echo Lang('is_blue_vip'); ?></option>
					 <option value="1"><?php echo Lang('blue_vip'); ?></option>
					 <option value="2"><?php echo Lang('year_blue_vip'); ?></option>
					</select>	
					<select name="blue_level">
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
				</p>
			</li>
			<li>
				<p>
					<?php echo Lang('between_level'); ?>：	
					<input name="minlevel" type="text" value="0" size="1"> ~	
					<input name="maxlevel" type="text" value="0" size="1">
				</p>
				<p>
					<?php echo Lang('channel'); ?>：
					<input type="text" name="source" value="">
				</p>
			</li>
			<li>
				<p>
					<span style="width:30px;"><?php echo Lang('player'); ?>：</span>
					<input name="username" type="text" value="" size="20"> 
					<select name="searchtype">
					 <option value="1"><?php echo Lang('exact_search'); ?></option>
					 <option value="2"><?php echo Lang('fuzzy'); ?></option>
					 <option value="3"><?php echo Lang('player'); ?>ID</option>
					</select>
				</p>
				<p>
					<span style="width:30px;">IP号：</span>
					<input name="ip" type="text" value="" size="20" maxlength="20"> 
				</p>
			</li>
			<li class="nobg">
				<p>
					<input type="submit" name="getsubmit" id="get_search_submit" value="<?php echo Lang('search'); ?>" class="button_link">
					<input name="cid" type="hidden" value="<?php echo $data['cid'] ?>">
					<input name="sid" type="hidden" value="<?php echo $data['sid'] ?>">
					<input name="dogetSubmit" type="hidden" value="1">
				</p>
				<p>
				</p>
			</li>
		</ul>
		</form>
	</div>
	<div class="option_area2">
		<div>
			<div class="menu_search">
				<a href="javascript:;" class="order on" data=""><?php echo Lang('all') ?></a><span class="bar">|</span>
				<a href="javascript:;" class="order" data="level"><?php echo Lang('level') ?></a><span class="bar">|</span>
				<a href="javascript:;" class="order" data="vip">VIP</a><span class="bar">|</span>
				<a href="javascript:;" class="order" data="ingot"><?php echo Lang('ingot') ?></a><span class="bar">|</span>
				<a href="javascript:;" class="order" data="level"><?php echo Lang('consumption') ?></a><span class="bar">|</span>
				<a href="javascript:;" class="order" data="coins"><?php echo Lang('coins') ?></a><span class="bar">|</span>
				<a href="javascript:;" class="order" data="fame"><?php echo Lang('skill') ?></a><span class="bar">|</span>
				<a href="javascript:;" class="order" data="mission"><?php echo Lang('ord_replica'); ?></a><span class="bar">|</span>
				<a href="javascript:;" class="order" data="heromission"><?php echo Lang('hero_replica'); ?></a>
				<a href="javascript:;" class="order" data="wyhmission">按精英万妖皇副本</a>
			</div>
		</div>
		<div class="menu_view">
			<span class="view" id="singlepage">
				<a href="javascript:;" class="prev"><?php echo Lang('page_prev'); ?></a>
				<a href="javascript:;" class="next"><?php echo Lang('page_next'); ?></a>
			</span>
		</div>
	</div>

	<div class="content">
		<!-- Begin form elements -->
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<thead id="playerlistheader">
				<tr>
				    <th style="width:50px;">ID</th>
				    <th style="width:25%"><?php echo Lang('player_name') ?>/<?php echo Lang('player_nick') ?></th>
				    <th style="width:50px;"><?php echo Lang('level') ?></th>
				    <th style="width:50px;">VIP</th>
				    <th style="width:60px;"><?php echo Lang('over_total_ingot') ?></th>
				    <th style="width:60px;"><?php echo Lang('recharge_ingot') ?></th>
				    <th style="width:60px;"><?php echo Lang('coins') ?></th>
				    <th style="width:120px;"><?php echo Lang('last_login_time') ?></th>
				    <th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="playerlist">

			</tbody>
		</table>
	<!-- End form elements -->
	</div>
	<div class="pagination pager" id="pager"></div>
</div>
