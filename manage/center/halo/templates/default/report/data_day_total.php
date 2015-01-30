<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function(){
	var url = "<?php echo INDEX; ?>?m=report&c=data&v=ajax_day_list";
	Ha.common.ajax(url, 'json', 'day=<?php echo $data['day']; ?>', 'get', 'container', function(data){
		if (data.status == 0){
			if (data.count > 0){
				$( "#data_day_list" ).empty().append($( "#data_day_tpl" ).tmpl( data.list )).show();
			}else {
				$( "#data_day_list").html('<tr><td colspan="9" style="text-align: left">没有找到数据。</td></tr>');
			}
		}
	}, 1);
});
</script>
<script type="text/template" id="data_day_tpl">
<tr>
	<td><a href="#app=4&cpp=42&url=${encodeurl('report','servertotal','data','&cpp=42&sid=')}${sid}%26name%3D${encodeURI(name!=''? name : o_name)}" target="_blank">${ name!=''? name : o_name}&nbsp;</a></td>
	<td>${register_count==0 ? '-' : register_count}</td>
    <td>{{if create_count==0 && avg_create_count == 0}}-{{else}}${create_count}{{if avg_create_count>0}}<span class="graptitle">(${avg_create_count}%)</span>{{/if}}{{/if}}</td>
	<td>${login_count==0 ? '-' : login_count}</td>
	<td>${max_online_count==0 ? '-': max_online_count}</td>
	<td>${pay_player_count==0? '-': pay_player_count}</td>
	<td>${new_player==0? '-': new_player}</td>
	<td>${pay_num==0 ? '-': pay_num}</td>
	<td><span class="orangetitle">${pay_amount==0? '-':pay_amount}</span></td>
	<td><span class="greentitle">${arpu==0? '-': arpu}</span></td>
</tr>
</script>

<h2><span id="tt"><?php echo $data['day'].Lang('data_total'); ?></span></h2>
<div class="container" id="container">
	<div class="column cf" id="table_column">
		<div class="title">
			<div class="more" id="tblMore">
	            <div id="div_pop">

	            </div>
	        </div>
			详细数据</div>
		<div id="dataTable">
		<table>
		<thead>
		<tr id="dataTheadTr">
		    <th><?php echo Lang('server'); ?></th>
			<th><?php echo Lang('register_count'); ?></th>
			<th><?php echo Lang('create_count'); ?></th>
			<th><?php echo Lang('login_count'); ?></th>
			<th><?php echo Lang('max_online_count'); ?></th>
			<th><?php echo Lang('pay_person_num'); ?></th>
			<th><?php echo Lang('new_pay_user_num'); ?></th>
			<th><?php echo Lang('total_pay_times'); ?></th>
			<th><?php echo Lang('pay_money'); ?></th>
			<th>ARPU</th>
		</tr>
		</thead>
		<tbody id="data_day_list">
			   
		</tbody>
		</table>
		</div>
	</div>
</div>


	<!-- <div class="locationbq">
        <div class="locLeft">
            <a href="#app=5&cpp=57&url=<?php echo urlencode(WEB_URL.INDEX.'?m=report&c=data&v=daily') ?>"><?php echo Lang('data_daily') ?></a>
            <span>&gt;</span><?php echo $data['day']; ?>
        </div>
        <div class="logo"></div>
    </div>
 -->