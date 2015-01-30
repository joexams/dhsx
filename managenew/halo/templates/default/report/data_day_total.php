<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function(){
	$.ajax({
		url: '<?php echo INDEX; ?>?m=report&c=data&v=ajax_day_list', 
		data: 'day=<?php echo $data['day']; ?>',
		dataType: 'json',
		success: function(data){
			if (data.status == 0){
				$('#data_day_tpl').tmpl(data.list).appendTo('#data_day_list');
			}
		}
	});
});
</script>
<script type="text/template" id="data_day_tpl">
<tr>
	<td><a href="#app=5&cpp=57&url=${encodeurl('report','server_total','data','&cpp=57&sid=')}${sid}%26name%3D${encodeURI(name!=''? name : o_name)}" target="_blank">${ name!=''? name : o_name}&nbsp;</a></td>
	<td>${register_count==0 ? '-' : register_count}</td>
    <td>{{if create_count==0 && avg_create_count == 0}}-{{else}}${create_count}{{if avg_create_count>0}}<span class="graptitle">(${avg_create_count}%)</span>{{/if}}{{/if}}</td>
	<td>${login_count==0 ? '-' : login_count}</td>
	<td>${max_online_count==0 ? '-': max_online_count}</td>
	<td>${pay_player_count==0? '-': pay_player_count}</td>
	<td>${pay_num==0 ? '-': pay_num}</td>
	<td><span class="orangetitle">${pay_amount==0? '-':pay_amount}</span></td>
	<td><span class="greentitle">${arpu==0? '-': arpu}</span></td>
	<td>&nbsp;</td>
</tr>
</script>
<div id="bgwrap">
	<div class="locationbq">
        <div class="locLeft">
            <a href="#app=5&cpp=57&url=<?php echo urlencode(WEB_URL.INDEX.'?m=report&c=data&v=daily') ?>"><?php echo Lang('data_daily') ?></a>
            <span>&gt;</span><?php echo $data['day']; ?>
        </div>
        <div class="logo"></div>
    </div>

	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo $data['day'].Lang('data_total'); ?></span></a></li>
	</ul>
	<br class="clear">
	<div class="content">
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th style="width:15%"><?php echo Lang('server'); ?></th>
					<th style="width:5%"><?php echo Lang('register_count'); ?></th>
					<th style="width:10%"><?php echo Lang('create_count'); ?></th>
					<th style="width:5%"><?php echo Lang('login_count'); ?></th>
					<th style="width:5%"><?php echo Lang('max_online_count'); ?></th>
					<th style="width:5%"><?php echo Lang('pay_person_num'); ?></th>
					<th style="width:5%"><?php echo Lang('total_pay_times'); ?></th>
					<th style="width:5%"><?php echo Lang('pay_money'); ?></th>
					<th style="width:5%">ARPU</th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="data_day_list">
				
			</tbody>
		</table>
	</div>
</div>