<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function(){
	var url = '<?php echo INDEX; ?>?m=report&c=player&v=arena';
	Ha.common.ajax(url, 'json', {'format': 'json', sid: "<?php echo $data['sid'] ?>"}, 'get', 'container', function(data){
		if (data.status == 0){
			$('#rankinglisttpl').tmpl(data.list).appendTo('#rankinglist');
		}else {
			$( "#rankinglist").html('<tr><td colspan="8" style="text-align: left">没有找到竞技场数据。</td></tr>').show();
		}
	}, 1);
});
</script>

<script type="text/template" id="rankinglisttpl">
<tr>
    <td class="num">${ranking}</td>
    <td>
    <a href="#app=4&cpp=35&url=${encodeurl('report', 'player_info', 'player', '&sid=<?php echo $data['sid'] ?>&sname=<?php echo $data['title'] ?>&id=')}${id}" data-id="${id}" data-username="${username}" class="userinfo">${username}</a>
    <br>
    <span class="bluetitle">${nickname}</span>
    {{if vip_level > 0}}
	<img class="grade lvl_img" src="<?php echo WEB_URL; ?>static/images/V${vip_level}.png">
	{{/if}}

    {{if is_tester == 1}}
		 <span class="redtitle"><?php echo Lang('tester'); ?></span>
	{{else is_tester == 2}}
		 <span class="redtitle"><?php echo Lang('senior_tester'); ?></span>
	{{else is_tester == 3}}
		 <span class="redtitle">GM</span>
	{{else is_tester == 4}}
		 <span class="redtitle"><?php echo Lang('newer_guide'); ?></span>
	{{/if}}
    </td>
    <td class="num">${last_ranking}</td>
    <td class="num">{{if challenged_times_today > 0}}<strong class="greentitle">${challenged_times_today}</strong>{{else}}--{{/if}}</td>
    <td>
    {{if last_challenge_time > 0}}${date('Y-m-d H:i', last_challenge_time)}{{else}}--{{/if}}
    </td>
    <td class="num">{{if buy_times_today > 0}}<strong class="redtitle">${buy_times_today}</strong>{{else}}--{{/if}}</td>
    <td>
    {{if last_buy_time > 0}}${date('Y-m-d H:i', last_buy_time)}{{else}}--{{/if}}
    </td>
</tr>
</script>

<h2><span id="tt"><?php echo Lang('玩家数据'); ?>：
<a href="#app=4&cpp=35&url=<?php echo urlencode(WEB_URL.INDEX.'?m=report&c=player&v=init'); ?>"><?php echo Lang('game_server') ?></a>
<span>&gt;</span><?php echo $data['title']; ?>
<span>&gt;</span><?php echo Lang('arena'); ?></span></h2>
<div class="container" id="container">
	<?php include template('report', 'player_top'); ?>
	<div class="column cf" id="table_column">
	<div id="dataTable">
	<table>
	<thead>
	<tr id="dataTheadTr">
	    <th class="num"><?php echo Lang('ranking') ?></th>
	    <th><?php echo Lang('player') ?></th>
	    <th class="num"><?php echo Lang('last_ranking') ?></th>
	    <th class="num"><?php echo Lang('today_pk') ?></th>
	    <th><?php echo Lang('last_pk_time') ?></th>
	    <th class="num"><?php echo Lang('today_pay') ?></th>
	    <th><?php echo Lang('last_pay_time') ?></th>
	</tr>
	</thead>
	<tbody id="rankinglist">
		   
	</tbody>
	</table>
	</div>
</div>
</div>