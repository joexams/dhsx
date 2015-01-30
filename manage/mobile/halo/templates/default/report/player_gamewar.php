<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function(){
	$('#gamewarlist').on('click', 'a.player_info', function(){
		var playerid = $(this).attr('data-pid'), sid = $(this).attr('data-sid'), title = '<?php echo Lang('player') ?>' +$(this).attr('title');
		if (playerid > 0 && sid > 0){
			var url = '<?php echo INDEX; ?>?m=report&c=player&v=player_info';
			Ha.common.ajax(url, 'html', {id: playerid, sid: sid}, 'get', 'container', function(data){
				Ha.Dialog.show(data, title, '', 'player_info_'+playerid);
			}, 1);
		}
	});
});
</script>

<h2><span id="tt"><?php echo Lang('玩家数据'); ?>：
<a href="#app=4&cpp=35&url=<?php echo urlencode(WEB_URL.INDEX.'?m=report&c=player&v=init'); ?>"><?php echo Lang('game_server') ?></a>
<span>&gt;</span><?php echo $data['title']; ?>
<span>&gt;</span><?php echo Lang('群仙会'); ?></span></h2>
<div class="container" id="container">
	
	<?php include template('report', 'player_top'); ?>
	<div class="column cf" id="table_column">
		<div id="dataTable">
		<table>
		<thead>
		<tr id="dataTheadTr">
	    	<th>&nbsp;</th>
			<th><?php echo Lang('ranking'); ?></th>
			<th><?php echo Lang('player'); ?></th>
		</tr>
		</thead>
		<tbody id="gamewarlist">
		   <?php if ($sky_war) { ?>
		   <?php foreach ($sky_war as $key => $slist) { ?>
		   	<?php foreach ($slist as $k => $player_id) { ?>
		   <tr>
		   	<td class="num"><?php echo $player_id; ?></td>
	       <td><?php echo $ranking[$key]; ?></td>
	       <td>
	       <a href="javascript:;" data-sid="<?php echo $data['sid'] ?>" data-pid="<?php echo $player_id; ?>" class="player_info" title="<?php echo $list[$player_id]['username']; ?>"><?php echo $list[$player_id]['username']; ?></a><br>
	       <span class="bluetitle"><?php echo $list[$player_id]['nickname']; ?></span>
	        <?php if ($list[$player_id]['vip_level'] > 0) { ?>
			<img class="grade lvl_img" src="<?php echo WEB_URL; ?>static/images/V<?php echo $list[$player_id]['vip_level']?>.png">
			<?php } ?>
	       <?php if ($list[$player_id]['is_tester'] == 1) { ?>
	        <span class="redtitle"><?php echo Lang('tester'); ?></span>
	       <?php }else if ($list[$player_id]['is_tester'] == 2) { ?>
	        <span class="redtitle"><?php echo Lang('senior_tester'); ?></span>
	       <?php }else if ($list[$player_id]['is_tester'] == 3) { ?>
	        <span class="redtitle">GM</span>
	       <?php }else if ($list[$player_id]['is_tester'] == 4) { ?>
	        <span class="redtitle"><?php echo Lang('newer_guide'); ?></span>
	       <?php } ?>
	       </td>
		   </tr>
		   	<?php } ?>
		   <?php } ?>
		   <?php }else { ?>
		   <tr><td colspan="3" style="text-align: left">没有找到群仙会数据。</td></tr>
		   <?php } ?>
		</tbody>
		</table>
		</div>
	</div>
</div>
</div>