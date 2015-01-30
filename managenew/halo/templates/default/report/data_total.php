<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">

$(document).ready(function(){
 	/**
 	 * 运营平台
 	 */
 	setTimeout(function() {
 		if (typeof global_companylist != 'undefined') {
 			$('#companyultpl').tmpl(global_companylist).appendTo('#companyul');
 			$('#companyul').change();
 		}
 	}, 250);
 	
 	$('#companyul').on('change', function(){
 		var cid = $('#companyul').val();
 		if (cid > 0){
			$('#data_total_list').html('<tr><td colspan="13">正在努力加载中...</td></tr>');

	 		$.ajax({
	 			url: '<?php echo INDEX; ?>?m=report&c=data&v=ajax_total_list',
	 			data: {cid: cid}, 
	 			dataType: 'json',
	 			success: function(data){
	 				$('#data_total_list').empty();
	 				if (data.status  == 0){
	 					$('#data_total_tpl').tmpl(data.list).appendTo('#data_total_list');
	 				}else {
	 					$('#list_op_tips').attr('class', 'alert_warning');
	 					$('#list_op_tips').children('p').html(data.msg);
	 					$('#list_op_tips').fadeIn();
	 					setTimeout( function(){
	 						$('#list_op_tips').fadeOut();
	 					}, ( 2000 ) );
	 				}
	 			},
				error: function() {
	 				$('#data_total_list').html('<tr><td colspan="13">数据加载失败...</td></tr>');
	 			}
	 		});
	 	}
 	});
 });
</script>

<script type="text/template" id="companyultpl">
<option value="${cid}" rel="#app=4&url=${encodeurl('<?php echo $data['url']['m']; ?>', '<?php echo $data['url']['v']; ?>', '<?php echo $data['url']['c']; ?>', '&cid=')}${cid}">${fn} - ${name}</option>
</script>

<script type="text/tempalte" id="data_total_tpl">
	<tr>
		<td><a href="#app=5&cpp=56&url=${encodeurl('report','server_total','data','&cpp=56&sid=')}${sid}%26name%3D${encodeURI(name!=''? name : o_name)}" target="_blank">${ name!=''? name : o_name}</a></td>
	    <td>${opendate}天</td>
	    <td>${register_count==0 ? '-' : register_count}</td>
    	<td>{{if create_count==0 && avg_create_count == 0}}-{{else}}${create_count}{{if avg_create_count>0}}<span class="graptitle">(${avg_create_count}%)</span>{{/if}}{{/if}}</td>
	    <td>${max_online_count==0 ? '-' : max_online_count}</td>
	    <td>${online_count==0 ? '-' : online_count}</td>
	    <td>${pay_player_count==0 ? '-' : pay_player_count}</td>
	    <td>${pay_num==0 ? '-' : pay_num}</td>
	    <td><span class="orangetitle">${pay_amount==0? '-':pay_amount}</span></td>
	    <td><span class="greentitle">${arpu==0? '-': arpu}</span></td>
	    <td>${max_player_level>0 ? max_player_level+'级' : '-'}</td>
	    <td>${consume>0 ? consume: '-'}</td>
	    <td>&nbsp;</td>
	</tr>
</script>

<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('data_total') ?></span></a></li>
	</ul>
	<br class="clear">
	<div class="h_lib_nav clear">
		<strong>运营平台选择：</strong>
		<select name="cid" id="companyul">
		</select>
	</div>

	<div class="content">
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th><?php echo Lang('server'); ?></th>
					<th><?php echo Lang('open_server_days'); ?></th>
					<th><?php echo Lang('register_count'); ?></th>
					<th><?php echo Lang('create_count'); ?></th>
					<th><?php echo Lang('max_online_count'); ?></th>
					<th><?php echo Lang('avg_online'); ?></th>
					<th><?php echo Lang('pay_person_num'); ?></th>
					<th><?php echo Lang('total_pay_times'); ?></th>
					<th><?php echo Lang('pay_money'); ?></th>
					<th>ARPU</th>
					<th><?php echo Lang('max_level') ?></th>
					<th><?php echo Lang('consumption'); ?></th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="data_total_list">
				
			</tbody>
		</table>

		<div id="list_op_tips" style="display: none;"><p></p></div>
	</div>
</div>
