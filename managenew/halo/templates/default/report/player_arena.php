<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function(){
	// $('.dash1').on('click', 'a.othermenu', function(){
	// 	var obj = $(this), url = obj.attr('rel');
	// 	if (url != ''){
	// 		url = url + '&title=<?php echo $data["title"] ?>';
	// 		// $('#container').load(url,function(response,status){});
	// 	}
	// });

	$.ajax({
		url: '<?php echo INDEX; ?>?m=server&c=get&v=arena_ranking',
		data: {sid: "<?php echo $data['sid'] ?>"},
		dataType: 'json',
		success: function(data){
			if (data.status == 0){
				$('#rankinglisttpl').tmpl(data.list).appendTo('#rankinglist');
			}
		}
	});

	$('#rankinglist').on('click', 'a.player_info', function(){
		var playerid = $(this).attr('data-pid'), sid = $(this).attr('data-sid'), title = '<?php echo Lang('player') ?>' +$(this).attr('title');
		if (playerid > 0 && sid > 0){
			dialog = $.dialog({id: 'player_info_'+playerid, width: 880, title: title});
			$.ajax({
				url: '<?php echo INDEX; ?>?m=report&c=player&v=detail_info',
				data: {id: playerid, sid: sid},
				success: function(data){
					dialog.content(data);
				}
			});
		}
	});
});
</script>

<script type="text/template" id="rankinglisttpl">
<tr>
    <th>${ranking}</th>
    <td>
    <a href="javascript:;" data-sid="<?php echo $data['sid'] ?>" data-pid="${id}" class="player_info" title="${username}">${username}</a><br>
    <span class="bluetitle">(${nickname})</span>
    {{if is_tester == 1}}
		 - <span><?php echo Lang('tester'); ?></span>
	{{else is_tester == 2}}
		 - <span><?php echo Lang('senior_tester'); ?></span>
	{{else is_tester == 3}}
		 - <span>GM</span>
	{{else is_tester == 4}}
		 - <span><?php echo Lang('newer_guide'); ?></span>
	{{else vip_level > 0}}
		 - <span style="color:red">VIP：${vip_level}</span>
	{{/if}}
    </td>
    <td>${last_ranking}</td>
    <td>{{if challenged_times_today > 0}}${challenged_times_today}{{else}}-{{/if}}</td>
    <td>
    {{if last_challenge_time > 0}}${date('Y-m-d H:i', last_challenge_time)}{{else}}-{{/if}}
    </td>
    <td>{{if buy_times_today > 0}}${buy_times_today}{{else}}-{{/if}}</td>
    <td>
    {{if last_buy_time > 0}}${date('Y-m-d H:i', last_buy_time)}{{else}}-{{/if}}
    </td>
    <td>&nbsp;</td>
</tr>
</script>

<div id="bgwrap">
	<div class="locationbq">
        <div class="locLeft">
            <a href="#app=5&cpp=24&url=<?php echo urlencode(WEB_URL.INDEX.'?m=report&c=player&v=init'); ?>"><?php echo Lang('game_server') ?></a>
            <span>&gt;</span><?php echo $data['title']; ?>
            <span>&gt;</span><?php echo Lang('arena') ?>
        </div>
        <div class="logo"></div>
    </div>
	<ul class="dash1">
		<li class="fade_hover"><a href="<?php echo $url1 ?>" rel="<?php echo INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=detail_list&sid='.$data['sid'].'&cid='.$data['cid'].'' ?>" class="othermenu"><span><?php echo Lang('player_list') ?></span></a></li>
		<li class="fade_hover"><a href="<?php echo $url2 ?>" rel="<?php echo INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=faction&sid='.$data['sid'].'&cid='.$data['cid'].'' ?>" class="othermenu"><span><?php echo Lang('faction') ?></span></a></li>
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('arena') ?></span></a></li>
		<li class="fade_hover"><a href="<?php echo $url4 ?>" rel="<?php echo INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=gamelog&sid='.$data['sid'].'&cid='.$data['cid'].'' ?>" class="othermenu"><span><?php echo Lang('game_log') ?></span></a></li>
		<li class="fade_hover"><a href="<?php echo $url5 ?>" rel="<?php echo INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=gamewar&sid='.$data['sid'].'&cid='.$data['cid'].'' ?>" class="othermenu"><span>群仙会</span></a></li>
	</ul>
	<br class="clear">

	<div class="content">
		<!-- Begin form elements -->
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
				    <th style="width:50px"><?php echo Lang('ranking') ?></th>
				    <th style="width:250px"><?php echo Lang('player') ?></th>
				    <th style="width:60px"><?php echo Lang('last_ranking') ?></th>
				    <th style="width:60px"><?php echo Lang('today_pk') ?></th>
				    <th style="width:120px"><?php echo Lang('last_pk_time') ?></th>
				    <th style="width:80px"><?php echo Lang('today_pay') ?></th>
				    <th style="width:120px"><?php echo Lang('last_pay_time') ?></th>
				    <th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="rankinglist">

			</tbody>
		</table>
	<!-- End form elements -->
	</div>
</div>