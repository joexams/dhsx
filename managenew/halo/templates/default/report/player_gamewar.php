<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function(){
	$('#gamewarlist').on('click', 'a.player_info', function(){
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

<div id="bgwrap">
	<div class="locationbq">
        <div class="locLeft">
            <a href="#app=5&cpp=24&url=<?php echo urlencode(WEB_URL.INDEX.'?m=report&c=player&v=init'); ?>"><?php echo Lang('game_server') ?></a>
            <span>&gt;</span><?php echo $data['title']; ?>
            <span>&gt;</span><?php echo Lang('game_log'); ?>
        </div>
        <div class="logo"></div>
    </div>
	<ul class="dash1">
		<li class="fade_hover"><a href="<?php echo $url1 ?>" rel="<?php echo INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=detail_list&sid='.$data['sid'].'&cid='.$data['cid'].'' ?>" class="othermenu"><span><?php echo Lang('player_list') ?></span></a></li>
		<li class="fade_hover"><a href="<?php echo $url2 ?>" rel="<?php echo INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=faction&sid='.$data['sid'].'&cid='.$data['cid'].'' ?>" class="othermenu"><span><?php echo Lang('faction') ?></span></a></li>
		<li class="fade_hover bubble"><a href="<?php echo $url3 ?>" rel="<?php echo INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=arena&sid='.$data['sid'].'&cid='.$data['cid'].'' ?>" class="othermenu"><span><?php echo Lang('arena') ?></span></a></li>
		<li class="fade_hover"><a href="<?php echo $url4 ?>" rel="<?php echo INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=gamelog&sid='.$data['sid'].'&cid='.$data['cid'].'' ?>" class="othermenu"><span><?php echo Lang('game_log') ?></span></a></li>
		<li class="fade_hover selected"><a href="javascript:;"><span>群仙会</span></a></li>
	</ul>

	<br class="clear">
	<div class="content">
		<!-- Begin form elements -->
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th style="width:60px;">ID</th>
					<th style="width:60px;"><?php echo Lang('ranking'); ?></th>
					<th style="width:250px;"><?php echo Lang('player'); ?></th>
				    <th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="gamewarlist">
				<?php foreach ($sky_war as $key => $slist) { ?>
					<?php foreach ($slist as $k => $player_id) { ?>
				<tr>
					<td><?php echo $player_id; ?></td>
				    <td><?php echo $ranking[$key]; ?></td>
				    <td>
				    <a href="javascript:;" data-sid="<?php echo $data['sid'] ?>" data-pid="<?php echo $player_id; ?>" class="player_info" title="<?php echo $list[$player_id]['username']; ?>"><?php echo $list[$player_id]['username']; ?></a><br>
				    <span class="bluetitle"><?php echo $list[$player_id]['nickname']; ?></span>
				    <?php if ($list[$player_id]['is_tester'] == 1) { ?>
				     - <span><?php echo Lang('tester'); ?></span>
				    <?php }else if ($list[$player_id]['is_tester'] == 2) { ?>
				     - <span><?php echo Lang('senior_tester'); ?></span>
				    <?php }else if ($list[$player_id]['is_tester'] == 3) { ?>
				     - <span>GM</span>
				    <?php }else if ($list[$player_id]['is_tester'] == 4) { ?>
				     - <span><?php echo Lang('newer_guide'); ?></span>
				    <?php }else if ($list[$player_id]['vip_level'] > 0) { ?>
				     - <span style="color:red">VIP：<?php echo $list[$player_id]['vip_level']; ?></span>
				    <?php } ?>
				    </td>
				    <td>&nbsp;</td>
				</tr>
					<?php } ?>
				<?php } ?>
			</tbody>
		</table>
	<!-- End form elements -->
	</div>
</div>