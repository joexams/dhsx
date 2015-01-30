<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function(){

	Ha.common.ajax("<?php echo INDEX; ?>?m=server&c=get&v=player_lossnewer", 'json', 'sid=<?php echo $sid ?>', 'get', 'container', function(data){
		if (data.status == 0){
			$('#player_1_level').html(data.list.player_1_level);
			$('#quser_1').html(data.list.quser_1);
			$('#quser_3').html(data.list.quser_3);
			$('#quser_1_no').html(data.list.quser_1_no);
			$('#quser_2').html(data.list.quser_2);
			$('#no_kill').html(data.list.no_kill);
			$('#no_item').html(data.list.no_item);
			$('#no_move').html(data.list.no_move);
			$('#player_num').html(data.list.player_num);
			$('#player_no_role').html(data.list.player_no_role);
			$('#player_2_level').html(data.list.player_2_level);
			$('#mission').html(data.list.mission);
		}
	}, 1);
});
</script>

<div class="container" id="container">
	<div class="column cf" id="table_column">
		<div id="dataTable">
		<table>
		<thead>
		<tr id="dataTheadTr">
		<th class="num">&nbsp;</th>
	    <th>人数</th>
		</tr>
		</thead>
		<tbody>
		<tr>
		<td style="text-align:right">总注册数</td>
		<td id="player_num">-</td>
		</tr>
		<tr>
		<td style="text-align:right">没有创建角色数</td>
		<td id="player_no_role">-</td>
		</tr>
		<tr>
		<td style="text-align:right">到达2级的人数</td>
		<td id="player_2_level">-</td>
		</tr>
		<tr>
		<td style="text-align:right">未到2级的人数</td>
		<td id="player_1_level">-</td>
		</tr>
		<tr>
		<td style="text-align:right">未移动过</td>
		<td id="no_move">-</td>
		</tr>
		<tr>
		<td style="text-align:right">第一个任务未接过</td>
		<td id="quser_1">-</td>
		</tr>
		<tr>
		<td style="text-align:right">第一个任务完成未提交</td>
		<td id="quser_1_no">-</td>
		</tr> 
		<tr>
		<td style="text-align:right">第一个任务未完成</td>
		<td id="quser_3">-</td>
		  </tr> 
		  <tr>
		<td style="text-align:right">未接过第二个任务</td>
		<td id="quser_2">-</td>
		</tr>
		<tr>
		<td style="text-align:right">未穿过装备</td>
		<td id="no_item">-</td>
		</tr>  
		<tr>
		<td style="text-align:right">进第一副本未杀怪</td>
		<td id="mission">-</td>
		</tr> 
		<tr>
		<td style="text-align:right">未杀过任何怪</td>
		<td id="no_kill">-</td>
		</tr> 
		<tr>
		<td>&nbsp;</td>
		<td><strong class="bluetitle">以上只统计新入游戏等级在2级以下的非测试号玩家</strong></td>
		</tr>
		</tbody>
		</table>
		</div>
	</div>
</div>